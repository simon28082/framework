<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/16 16:16
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\Server\Contracts;

/**
 * Interface StartActionContract
 * @package CrCms\Foundation\Swoole\Server\Contracts
 */
interface StartActionContract
{
    /**
     * @return bool
     */
    public function start(): bool;

    /**
     * @return bool
     */
    public function stop(): bool;

    /**
     * @return bool
     */
    public function restart(): bool;
}