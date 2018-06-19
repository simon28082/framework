<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/16 12:11
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\Server;

use CrCms\Foundation\Swoole\Server\Contracts\ServerContract;
use CrCms\Foundation\Swoole\Server\Contracts\StartActionContract;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Swoole\Process;
use UnexpectedValueException;
use RuntimeException;
use CrCms\Foundation\Swoole\Traits\ProcessNameTrait;
use function CrCms\Foundation\App\Helpers\array_merge_recursive_distinct;
use function CrCms\Foundation\App\Helpers\framework_config_path;

/**
 * Class ServerManage
 * @package CrCms\Foundation\Swoole\Server
 */
class ServerManage implements StartActionContract
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * @var array
     */
    protected $pids;

    /**
     * @var
     */
    protected $config;

    /**
     * ServerManage constructor.
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
        $this->loadConfiguration();
    }

    /**
     * @return bool
     */
    public function start(): bool
    {
        if ($this->pidExists() && $this->processExists()) {
            throw new UnexpectedValueException('Swoole server is running');
        }

        $processes = $this->processes();

        $pids = $processes->map(function (Process $process) {
            return $process->start();
        });

        $this->addLogProcess($processes);

        return $this->storePid(collect($pids));
    }

    /**
     * @param Collection $processes
     */
    protected function addLogProcess(Collection $processes)
    {
        $logProcess = new Process(function (Process $mainProcess) use ($processes) {
            $processes->each(function (Process $process, $key) {
                swoole_event_add($process->pipe, function () use ($process) {
                    $result = $process->read();
                    swoole_async_write(storage_path('run.log'), $result);
                });
            });

            /*swoole_event_add($mainProcess->pipe, function () use ($mainProcess) {
                $result = $mainProcess->read();
                swoole_async_write(storage_path('run.log'), $result);
            });*/
        }, false, true);
        $logProcess->name('swoole_log');
        $pid = $logProcess->start();
        $this->storeLogPid($pid);
    }

    /**
     * @return Collection
     */
    protected function processes(): Collection
    {
        return $this->servers()->map(function (ServerContract $server, int $key) {
            $process = new Process(function (Process $process) use ($server, $key) {
                // 经过测试放在 Process内外都可以
                // 放内，在进程内再创建Server，合理一点
                $server->createServer();
                $server->setProcess($process);
                $server->start();


            }, false, true);//, true, false

            return $process;
        });
    }

    /**
     * @return bool
     */
    public function stop(): bool
    {
        if (!$this->processExists()) {
            throw new UnexpectedValueException('Swoole server is not running');
        }

        $this->currentPids()->map(function ($pid) {
            if (!Process::kill($pid)) {
                throw new RuntimeException("The process[pid:{$pid}] kill error");
            }
        });

        Process::kill($this->currentLogPid());

        $this->deleteLogPid();

        Process::wait();

        return $this->deletePid();
    }

    /**
     * @return bool
     */
    public function restart(): bool
    {
        $this->stop();

        sleep(3);

        return $this->start();
    }

    /**
     * @param int $pid
     * @return int
     */
    protected function storePid(Collection $pids): int
    {
        return file_put_contents($this->config['pid_file'], $pids->implode(','));
    }

    /**
     * @param int $pid
     * @return int
     */
    protected function storeLogPid(int $pid): int
    {
        return file_put_contents($this->config['log_pid_file'], $pid);
    }

    /**
     * @return bool
     */
    protected function deleteLogPid()
    {
        $result = @unlink($this->config['log_pid_file']);
        if (!$result) {
            throw new RuntimeException("Remove pid file : [{$this->config['log_pid_file']}] error");
        }

        return $result;
    }

    /**
     * @return bool
     */
    protected function deletePid(): bool
    {
        if (!$this->pidExists()) {
            return true;
        }

        $result = @unlink($this->config['pid_file']);
        if (!$result) {
            throw new RuntimeException("Remove pid file : [{$this->config['pid_file']}] error");
        }

        return $result;
    }

    /**
     * @return bool
     */
    protected function processExists(): bool
    {
        $pids = $this->currentPids();

        return !$pids->map(function ($pid) {
            return Process::kill($pid, 0);
        })->filter(function ($exists) {
            return $exists;
        })->isEmpty();
    }

    /**
     * @return bool
     */
    protected function pidExists(): bool
    {
        return file_exists($this->config['pid_file']) && filesize($this->config['pid_file']) > 0;
    }

    protected function currentLogPid()
    {
        return file_get_contents($this->config['log_pid_file']);
    }

    /**
     * @return Collection
     */
    protected function currentPids(): Collection
    {
        if (!$this->pidExists()) {
            return collect([]);
        }

        $pids = file_get_contents($this->config['pid_file']);
        return collect(explode(',', $pids))->map(function ($pid) {
            return intval($pid);
        });
    }

    /**
     *
     */
    protected function loadConfiguration(): void
    {
        $config = require framework_config_path('swoole.php');

        $customConfigPath = config_path('swoole.php');
        if (file_exists($customConfigPath) && is_file(file_exists($customConfigPath))) {
            $config = array_merge_recursive_distinct($config, require $customConfigPath);
        }

        $this->config = $config;
    }

    /**
     * @return Collection
     */
    protected function servers(): Collection
    {
        return collect($this->config['servers'])->map(function ($server) {
            $server['drive'] = $this->config['drives'][$server['drive']] ?? '';
            return $server;
        })->filter(function ($server) {
            return !empty($server['drive']) && class_exists($server['drive']);
        })->map(function ($server) {
            return new $server['drive']($this->app, $server);
        });
    }
}