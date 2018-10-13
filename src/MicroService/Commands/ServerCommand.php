<?php

namespace CrCms\Foundation\MicroService\Commands;

use CrCms\Foundation\Swoole\AbstractServerCommand;
use CrCms\Foundation\Swoole\Server\Contracts\ServerContract;

/**
 * Class ServerCommand
 * @package CrCms\Foundation\MicroService\Commands
 */
class ServerCommand extends AbstractServerCommand
{
    /**
     * @var string
     */
    protected $server = 'micro-service';

    /**
     * @return ServerContract
     */
    public function server(): ServerContract
    {
        return new \CrCms\Foundation\MicroService\Server(
            $this->getLaravel(),
            config("swoole.servers.{$this->server}")
        );
    }
}