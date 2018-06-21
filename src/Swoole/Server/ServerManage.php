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
use CrCms\Foundation\Swoole\Server\Processes\INotifyProcess;
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

    /**
     * @var
     */
    protected $processes;

    /**
     * @var \CrCms\Foundation\Swoole\Server\ProcessManage
     */
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
        $processes = $this->processes(
            $this->servers()
        );

        $pids = $this->startProcess($processes);

        $logPid = $this->addLogProcess($processes);

        $allPid = collect([
            'servers' => $pids->toArray(),
            'log' => $logPid
        ]);

        if ($this->config['notify']['monitor']) {
            $notifyPid = $this->addINotifyProcess();
            $allPid = $allPid->merge(['inotify'=>$notifyPid]);
        }

        return $this->processManage->store($allPid);
    }

    /**
     * @param Collection $processes
     * @return Collection
     */
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
     * @return int
     */
    protected function addINotifyProcess(): int
    {
        $notifyProcess = new INotifyProcess($this->processManage, $this->config);
        return $notifyProcess->start();
    }

    /**
     * @return Collection
     */
    protected function processes(Collection $servers): Collection
    {
        return $servers->map(function (ServerContract $server) {
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
            return $this->processManage->clean();
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function restart(): bool
    {
        if ($this->processManage->exists()) {
            $this->stop();
            sleep(4);
        }

        return $this->start();
    }

    /**
     * @return bool
     */
    public function reload(): bool
    {
        if (!$this->processManage->exists('servers')) {
            throw new UnexpectedValueException('Swoole server is not running');
        }

        return $this->processManage->kill(SIGUSR1, 'servers');
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