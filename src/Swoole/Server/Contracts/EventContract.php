<?php

namespace CrCms\Foundation\Swoole\Server\Contracts;

use CrCms\Foundation\Swoole\Server\AbstractServer;

interface EventContract
{
    /**
     * @return void
     */
    public function handle(AbstractServer $server): void;
}