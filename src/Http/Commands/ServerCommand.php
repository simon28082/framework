<?php

namespace CrCms\Foundation\Http\Commands;

use CrCms\Foundation\Swoole\AbstractServerCommand;
use CrCms\Foundation\Swoole\Server\Contracts\ServerContract;
use Illuminate\Filesystem\Filesystem;

/**
 * Class ServerCommand
 * @package CrCms\Foundation\Http\Commands
 */
class ServerCommand extends AbstractServerCommand
{
    /**
     * @var string
     */
    protected $server = 'http';

    /**
     * @return ServerContract
     */
    public function server(): ServerContract
    {
        $this->cleanRunCache();

        return new \CrCms\Foundation\Http\Server(
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