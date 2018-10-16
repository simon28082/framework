<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/17 14:25
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\Server\Contracts;

use Swoole\Server;

/**
 * Interface ServerContract
 * @package CrCms\Foundation\Swoole\Server\Contracts
 */
interface ServerContract
{
    /**
     * @return void
     */
    public function createServer(): void;

    /**
     * @return Server
     */
    public function getServer(): Server;

    /**
     * @return string
     */
    public function pidFile(): string;
}