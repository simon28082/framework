<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/16 12:11
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\Server;

use CrCms\Foundation\Start\Drivers\MicroService;
use CrCms\Foundation\Swoole\MicroService\Server;
use CrCms\Foundation\Swoole\Server\Contracts\ServerContract;
use CrCms\Foundation\Swoole\Server\Contracts\StartActionContract;
use CrCms\Foundation\Swoole\Server\Processes\INotifyProcess;
use CrCms\Foundation\Swoole\Server\Processes\LogProcess;
use CrCms\Foundation\Swoole\Server\Processes\ServerProcess;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use UnexpectedValueException;
use CrCms\Foundation\Swoole\Process\ProcessManager;

/**
 * Class ServerManager
 * @package CrCms\Foundation\Swoole\Server
 */
class ServerManager implements StartActionContract
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * @var
     */
    protected $config;

    /**
     * @var
     */
    protected $processes;

    /**
     * @var ProcessManager
     */
    protected $processManager;

    /**
     * @var $server
     */
    protected $server;

    /**
     * ServerManage constructor.
     * @param Container $app
     */
    public function __construct(Container $app, array $config, AbstractServer $server, ProcessManager $processManager)
    {
        $this->app = $app;
        $this->config = $config;
        $this->processManager = $processManager;
        $this->server = $server;
    }

    /**
     * @return bool
     */
    public function start(): bool
    {
//        if ($this->processManager->exists()) {
//            throw new UnexpectedValueException('Swoole server is running');
//        }
//        Process::daemon();
//        Process::signal(SIGCHLD, function ($sig) {
//            //必须为false，非阻塞模式
//            while ($ret = Process::wait(false)) {
//                echo "PID={$ret['pid']}\n";
//            }
//        });
//dd($this->config);
//        swoole_set_process_name('--process -==== mmain--');
//        sleep(10);
//
//        $pid = \CrCms\Foundation\Swoole\Process\ProcessManager::instance()->start(
//
//        );

        $this->processManager->start(
            new ServerProcess(
                $this->server
            ),
            'micro-service'
        );

//        file_put_contents(storage_path('ss'),$pid);
//
//        dump("=============pid {$pid}=============");

//        dump(\CrCms\Foundation\Swoole\Process\ProcessManager::instance()->list());

        return true;

        /* 这一块应该处理成类似中间件模块格式，装饰模式，暂时先这样 */
        $processes = $this->processes(
            $this->servers()
        );

        $pids = $this->startProcess($processes);

        $logPid = $this->addLogProcess($processes);

        $allPid = collect([
            'servers' => $pids->toArray(),
            'log' => $logPid
        ]);

        if ($this->config['notify']['monitor'] && function_exists('inotify_init')) {
            $notifyPid = $this->addINotifyProcess();
            $allPid = $allPid->merge(['inotify' => $notifyPid]);
        }

        return $this->processManager->store($allPid);
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
        $notifyProcess = new INotifyProcess($this->processManager, $this->config);
        return $notifyProcess->start();
    }

    /**
     * @return Collection
     */
    protected function processes(Collection $servers): Collection
    {
        return $servers->map(function (ServerContract $server) {
            //return new ServerProcess($server);

        });
    }

    /**
     * @return bool
     */
    public function stop(): bool
    {
        dump($this->processManager->kill('micro-service'));

        return true;

        if (!$this->processManager->exists()) {
            throw new UnexpectedValueException('Swoole server is not running');
        }

        if ($this->processManager->kill()) {
            return $this->processManager->clean();
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function restart(): bool
    {
        if ($this->processManager->exists()) {
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
        if (!$this->processManager->exists('servers')) {
            throw new UnexpectedValueException('Swoole server is not running');
        }

        return $this->processManager->kill(SIGUSR1, 'servers');
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