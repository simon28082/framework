<?php

namespace CrCms\Foundation\Swoole\Server\Events;

use CrCms\Foundation\Swoole\Server\AbstractServer;
use CrCms\Foundation\Swoole\Server\Contracts\EventContract;

/**
 * Class StartEvent
 * @package CrCms\Foundation\Swoole\Server\Events
 */
class StartEvent extends AbstractEvent implements EventContract
{
    /**
     * @param AbstractServer $server
     */
    public function handle(AbstractServer $server): void
    {
        parent::handle($server);

        parent::setEventProcessName('master');
    }
}