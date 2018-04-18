<?php

namespace CrCms\Foundation\Swoole\Events;

use CrCms\Foundation\Swoole\Server;
use Swoole\Server as SwooleServer;

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
    public function handle(Server $server): void
    {
        $this->server = $server;
        $this->swooleServer = $this->server->getSwooleServer();
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * @return SwooleServer
     */
    public function getSwooleServer(): SwooleServer
    {
        return $this->swooleServer;
    }
}