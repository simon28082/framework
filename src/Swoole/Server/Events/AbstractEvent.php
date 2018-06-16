<?php

namespace CrCms\Foundation\Swoole\Server\Events;

use CrCms\Foundation\Swoole\Server;
use Swoole\Server as SwooleServer;
use CrCms\Foundation\Swoole\Server\AbstractServer;
use CrCms\Foundation\Swoole\Server\Contracts\EventContract;

/**
 * Class AbstractEvent
 * @package CrCms\Foundation\Swoole\Events
 */
abstract class AbstractEvent implements EventContract
{
    /**
     * @var Server
     */
    protected $server;

    /**
     * @var SwooleServer
     */
    protected $swooleServer;

    /**
     * @param Server $server
     */
    public function handle(AbstractServer $server): void
    {
        $this->server = $server;
//        $this->swooleServer = $this->server->getSwooleServer();
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

//    /**
//     * @return SwooleServer
//     */
//    public function getSwooleServer(): SwooleServer
//    {
//        return $this->swooleServer;
//    }
}