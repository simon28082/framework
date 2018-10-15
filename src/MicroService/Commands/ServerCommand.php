<?php

namespace CrCms\Foundation\MicroService\Commands;

use CrCms\Foundation\Swoole\AbstractServerCommand;
use CrCms\Foundation\Swoole\Server\Contracts\ServerContract;
use Illuminate\Filesystem\Filesystem;

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
        $this->cleanRunCache();

        return new \CrCms\Foundation\MicroService\Server(
            $this->getLaravel(),
            config("swoole.servers.{$this->server}"),
            $this->server
        );
    }

    /**
     * @return void
     */
    protected function cleanRunCache(): void
    {
        (new Filesystem())->cleanDirectory(
            dirname($this->getLaravel()->getServerApplication()->getCachedServicesPath())
        );
    }
}