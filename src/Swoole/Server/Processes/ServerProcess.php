<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/19 21:25
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\Server\Processes;

use CrCms\Foundation\Swoole\Process\AbstractProcess;
use CrCms\Foundation\Swoole\Process\Contracts\ProcessContract;
use CrCms\Foundation\Swoole\Server\AbstractServer;
use CrCms\Foundation\Swoole\Server\Contracts\ServerContract;
use Swoole\Process;

/**
 * Class ServerProcess
 * @package CrCms\Foundation\Swoole\Server\Processes
 */
class ServerProcess extends AbstractProcess implements ProcessContract
{
    /**
     * @var AbstractServer
     */
    protected $server;

    /**
     * ServerProcess constructor.
     * @param AbstractServer $server
     * @param bool $redirectStdinStdout
     * @param bool $createPipe
     */
    public function __construct(AbstractServer $server, bool $redirectStdinStdout = false, int $createPipe = 0)
    {
        $this->server = $server;
        parent::__construct($redirectStdinStdout, $createPipe);
    }

    /**
     * @param Process $process
     */
    public function childProcess(Process $process): void
    {
        $this->server->createServer();
        $this->server->bootstrap();
        $this->server->setProcess($this->process);
        $this->server->start();

        //这两个进程不加也行，默认swoole会自动监听
        /*Process::signal(SIGTERM, function ($signo) {
            $this->server->stop();
        });

        Process::signal(SIGUSR1, function ($signo) {
            $this->server->restart();
        });*/
    }
}