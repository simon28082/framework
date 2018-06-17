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
use CrCms\Foundation\Swoole\Traits\ProcessNameTrait;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Swoole\Process;
use UnexpectedValueException;
use RuntimeException;
use function CrCms\Foundation\App\Helpers\array_merge_recursive_distinct;
use function CrCms\Foundation\App\Helpers\framework_config_path;

/**
 * Class ServerManage
 * @package CrCms\Foundation\Swoole\Server
 */
class ServerManage implements StartActionContract
{
    use ProcessNameTrait;

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
        if ($this->pidExists()) {
            throw new UnexpectedValueException('Swoole server is running');
        }

        $pids = $this->servers()->map(function (ServerContract $server, int $key) {
            $process = new Process(function () use ($server) {
                // 经过测试放在 Process内外都可以
                // 放内，在进程内再创建Server，合理一点
                $server->createServer();
                $server->start();
            }, true, false);
            $process->name('swoole_main_' . strval($key));
            //file_put_contents(storage_path('abc'),$process->read());
            return $process->start();
        });

        return $this->storePid($pids);
    }

    /**
     * @return bool
     */
    public function stop(): bool
    {
        if (!$this->pidExists()) {
            throw new UnexpectedValueException('Swoole server is not running');
        }

        $this->currentPids()->map(function ($pid) {
            if (!Process::kill($pid)) {
                throw new RuntimeException("The process[pid:{$pid}] kill error");
            }
        });

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
    protected function pidExists(): bool
    {
        return file_exists($this->config['pid_file']) && filesize($this->config['pid_file']) > 0;
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