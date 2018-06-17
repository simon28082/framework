<?php

namespace CrCms\Foundation\Swoole\Server\Events;

use CrCms\Foundation\Swoole\Server\AbstractServer;
use CrCms\Foundation\Swoole\Server\Contracts\EventContract;

/**
 * Class AbstractEvent
 * @package CrCms\Foundation\Swoole\Server\Events
 */
abstract class AbstractEvent implements EventContract
{
    /**
     * @var AbstractServer
     */
    protected $server;

    /**
     * @param AbstractServer $server
     */
    public function handle(AbstractServer $server): void
    {
        $this->server = $server;
    }

    /**
     * @return AbstractServer
     */
    public function getServer(): AbstractServer
    {
        return $this->server;
    }
}