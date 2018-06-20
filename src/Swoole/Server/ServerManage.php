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
use CrCms\Foundation\Swoole\Server\Processes\LogProcess;
use CrCms\Foundation\Swoole\Server\Processes\ServerProcess;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Swoole\Process;
use UnexpectedValueException;
use RuntimeException;
use CrCms\Foundation\Swoole\Traits\ProcessNameTrait;
use CrCms\Foundation\Swoole\Server\ProcessManage;

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

    protected $processes;

    protected $processManage;

    /**
     * ServerManage constructor.
     * @param Container $app
     */
    public function __construct(Container $app, array $config, ProcessManage $processManage)
    {
        $this->app = $app;
        $this->config = $config;
        $this->processManage = $processManage;
    }

    /**
     * @return bool
     */
    public function start(): bool
    {
        if ($this->processManage->exists()) {
            throw new UnexpectedValueException('Swoole server is running');
        }
        /*Process::daemon();
        Process::signal(SIGCHLD, function ($sig) {
            //必须为false，非阻塞模式
            while ($ret = Process::wait(false)) {
                echo "PID={$ret['pid']}\n";
            }
        });*/

        /* 这一块应该处理成类似中间件模块格式，暂时先这样 */
        $processes = $this->processes();

        $pids = $this->startProcess($processes);
        $this->processManage->store($pids);

        $logPid = $this->addLogProcess($processes);
        $this->processManage->append($logPid);

        return true;
    }

    protected function startProcess(Collection $processes): Collection
    {
        return $processes->map(function (ServerProcess $process) {
            return $process->start();
        });
    }

    /**
     * @param Collection $processes
     */
    protected function addLogProcess(Collection $processes): int
    {
        $logProcess = new LogProcess($processes, storage_path('run.log'));
        return $logProcess->start();
    }

    /**
     * @return Collection
     */
    protected function processes(): Collection
    {
        return $this->servers()->map(function (ServerContract $server) {
            return new ServerProcess($server);
        });
    }

    /**
     * @return bool
     */
    public function stop(): bool
    {
        if (!$this->processManage->exists()) {
            throw new UnexpectedValueException('Swoole server is not running');
        }

        if ($this->processManage->kill()) {
            $this->processManage->clean();
        }

        return true;

//        return $this->processManage->kill();

//        if (!$this->processExists()) {
//            throw new UnexpectedValueException('Swoole server is not running');
//        }

        //SIGUSR1
        $this->currentPids()->map(function ($pid) {
            Process::kill($pid, SIGTERM);
            echo 'ssss==';
            /*if (!Process::kill($pid)) {
                throw new RuntimeException("The process[pid:{$pid}] kill error");
            }*/
        });
//        exit();
        return true;
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