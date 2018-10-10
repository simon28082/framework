<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/18 18:13
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\Process\Contracts;

use Swoole\Process;

interface ProcessContract
{
    /**
     * @param Process $process
     * @return void
     */
    public function handle(Process $process): void;

    /**
     * @return int
     */
    public function start(): int;

    /**
     * @return int
     */
    public function exit(): int;
}