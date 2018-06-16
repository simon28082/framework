<?php

namespace CrCms\Foundation\Swoole\Server\Events;

use CrCms\Foundation\Swoole\Server\AbstractServer;
use CrCms\Foundation\Swoole\Traits\ProcessNameTrait;
use CrCms\Foundation\Swoole\Server\Contracts\EventContract;

class StartEvent extends AbstractEvent implements EventContract
{
    use ProcessNameTrait;

    public function handle(AbstractServer $server): void
    {
        parent::handle($server);

//        static::setProcessName(config('swoole.process_prefix').'master');
    }
}