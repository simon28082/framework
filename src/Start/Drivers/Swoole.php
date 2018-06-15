<?php

namespace CrCms\Foundation\Start\Drivers;

use Carbon\Carbon;
use CrCms\Foundation\Swoole\INotify;
use CrCms\Foundation\Swoole\Server;
use CrCms\Foundation\StartContract;
use CrCms\Foundation\Swoole\ServerManage;
use CrCms\Foundation\Swoole\Traits\ProcessNameTrait;
use Illuminate\Contracts\Container\Container;
use Swoole\Async;
use Swoole\Process;
use Exception;
use UnexpectedValueException;
use Illuminate\Contracts\Http\Kernel;

/**
 * Class Swoole
 * @package CrCms\Foundation\Start\Drivers
 */
class Swoole implements StartContract
{
    use ProcessNameTrait;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $allows = ['start', 'stop', 'restart', 'reload'];

//    /**
//     * @var Server
//     */
//    protected $server;

    /**
     * @var ServerManage
     */
    protected $serverManage;

    /**
     * @var Container
     */
    protected $app;

    /**
     * @param Container $app
     * @param array $config
     * @return void
     */
    protected function setServerManage(): void
    {
        $this->serverManage = new ServerManage($this->app, $this->config);
    }

    /**
     * @param string $content
     * @return bool
     */
    protected function log(string $content): bool
    {
        return Async::writeFile(sprintf($this->config['error_log'], Carbon::now()->toDateString()), $content . PHP_EOL, null, FILE_APPEND);
    }

    /**
     * @return void
     */
    protected function initialization(): void
    {
        $this->setServerManage($this->app, $this->config);
    }

    /**
     * @return void
     */
    protected function bootstrapBaseMiddleware(): void
    {
        $this->app->make(Kernel::class)->bootstrap();
    }

    /**
     * @return void
     */
    protected function setConfig(): void
    {
        $this->config = $this->app->make('config')->get('swoole');
    }

    /**
     * @param Container $app
     * @param string $action
     * @return void
     */
    public function run(Container $app, array $params): void
    {
        $this->app = $app;

        $this->bootstrapBaseMiddleware();

        $this->setConfig();

        $action = $params[1] ?? 'start';

        if (in_array($action, $this->allows, true)) {
            try {
                $this->{$action}();
            } catch (Exception $exception) {
                $this->log($exception->getMessage());
                echo $exception->getMessage() . PHP_EOL;
            }
        } else {
            echo "Allow only " . implode($this->allows, ' ') . "options" . PHP_EOL;
        }
    }

    /**
     * @return void
     */
    protected function start(): void
    {
        if ($this->killProcess($this->getPid(), 0)) {
            throw new UnexpectedValueException('The process already exists and cannot be opened again');
        }

        $this->setServerManage();

        try {
            $process = new Process(function (Process $process) {
                if ($this->config['notify']['monitor']) {
                    $this->setINotifyProcess();
                }

                $this->serverManage->start();
            });

            $pid = $process->start();

            $this->setPid($pid);

            echo "Server PID[{$pid}] start success" . PHP_EOL;

        } catch (Exception $exception) {
            $this->stop();
            $this->log($exception->getMessage());
            echo $exception->getMessage() . PHP_EOL;
        }
    }

    /**
     * @return void
     */
    protected function stop(): void
    {
        $pid = $this->getPid();

        if (!$this->killProcess($pid, 0)) {
            throw new UnexpectedValueException('The process does not exist and cannot terminate the process');
        }

        try {
            if ($this->killProcess($pid, SIGTERM)) {
                @unlink($this->config['pid_file']);
            }
            echo "The process[{$pid}] is stopped" . PHP_EOL;
        } catch (Exception $exception) {
            $this->log($exception);
            echo $exception->getMessage() . PHP_EOL;
        }
    }

    /**
     * @return void
     */
    protected function restart(): void
    {
        $this->stop();
        sleep(3);
        $this->start();

        echo "The process is restart" . PHP_EOL;
    }

    /**
     * @return void
     */
    protected function reload(): void
    {
        $pid = $this->getPid();
        if (!$this->killProcess($pid, 0)) {
            throw new UnexpectedValueException('The process does not exist and cannot be restarted');
        }

        try {
            if ($this->killProcess($pid, SIGUSR1)) {
                echo "The process is reload" . PHP_EOL;
            }
        } catch (Exception $exception) {
            $this->log($exception);
            echo $exception->getMessage() . PHP_EOL;
        }
    }

    /**
     * @param int $pid
     * @param int $sign
     * @return bool
     */
    protected function killProcess(int $pid, int $sign): bool
    {
        return Process::kill($pid, $sign);
    }

    /**
     * @return void
     */
    protected function setINotifyProcess(): void
    {
        $iNotify = new INotify($this->config['notify']['targets']);

        $iNotifyProcess = new Process(function (Process $process) use ($iNotify) {
            static::setProcessName($this->config['process_prefix'] . 'notify');
            $iNotify->monitor(function ($events) {
                if (!empty($events)) {
                    $this->serverManage->reload();
                    Async::writeFile($this->config['notify']['log_path'], 'The notify process is reload' . Carbon::now()->toDateTimeString() . PHP_EOL, null, FILE_APPEND);
                }
            });
        });

        $this->serverManage->getMaster()->getSwooleServer()->addProcess($iNotifyProcess);
    }

    /**
     * @return int
     */
    protected function getPid(): int
    {
        if (file_exists($this->config['pid_file'])) {
            $pid = file_get_contents($this->config['pid_file']);
            if (is_numeric($pid)) {
                return intval($pid);
            }
        }

        return -100000000;
    }

    /**
     * @param int $pid
     * @return int
     */
    protected function setPid(int $pid): int
    {
        return file_put_contents($this->config['pid_file'], $pid);
    }
}