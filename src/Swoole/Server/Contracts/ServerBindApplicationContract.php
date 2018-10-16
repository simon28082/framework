<?php

namespace CrCms\Foundation\Swoole\Server\Contracts;

/**
 * Interface ServerBindApplicationContract
 * @package CrCms\Foundation\Swoole\Server\Contracts
 */
interface ServerBindApplicationContract
{
    /**
     * @param ServerContract $server
     * @return void
     */
    public function bindServer(ServerContract $server): void;

    /**
     * @return ServerContract
     */
    public function getServer(): ServerContract;
}