<?php

namespace CrCms\Foundation\Swoole\Server\Events;

use CrCms\Foundation\Swoole\Server\AbstractServer;
use CrCms\Foundation\Swoole\Server\Contracts\EventContract;

class CloseEvent extends AbstractEvent implements EventContract
{
    protected $fd;

    protected $reactorId;

    /**
     * CloseEvent constructor.
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     */
    public function __construct(int $fd, int $reactorId)
    {
        $this->reactorId = $reactorId;
    }

    public function handle(AbstractServer $server): void
    {
    }
}