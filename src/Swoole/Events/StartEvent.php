<?php

namespace CrCms\Foundation\Swoole\Events;

use CrCms\Foundation\Swoole\Server;
use CrCms\Foundation\Swoole\Traits\ProcessNameTrait;

class StartEvent extends AbstractEvent implements EventContract
{
    use ProcessNameTrait;

    public function handle(Server $server): void
    {
        parent::handle($server);

        static::setProcessName($this->server->getConfig()['process_prefix'].'master');
    }
}