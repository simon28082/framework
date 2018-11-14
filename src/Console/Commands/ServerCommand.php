<?php

namespace CrCms\Framework\Console\Commands;

use CrCms\Server\AbstractServerCommand;
use CrCms\Server\Server\Contracts\ServerContract;
use Illuminate\Filesystem\Filesystem;

/**
 * Class ServerCommand
 * @package CrCms\Framework\Http\Commands
 */
class ServerCommand extends AbstractServerCommand
{
    /**
     * @var string
     */
    protected $signature = 'server {action : start or stop or restart}';

    /**
     * @var string
     */
    protected $description = 'Swoole server';

    /**
     * @return ServerContract
     */
    public function server(): ServerContract
    {
        $this->cleanRunCache();

        return new \CrCms\Framework\Http\Server(
            $this->getLaravel(),
            config("swoole.servers.http"),
            'server.http'
        );
    }

    /**
     * @return void
     */
    protected function cleanRunCache(): void
    {
        (new Filesystem())->cleanDirectory(
            dirname($this->getLaravel()->getCachedServicesPath())
        );
    }
}