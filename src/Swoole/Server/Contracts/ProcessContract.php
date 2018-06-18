<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/18 18:13
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\Server\Contracts;

use Swoole\Process;

interface ProcessContract
{

//    public function create(callable $callback, string $name = '');


    public function start(): bool;


    public function stop(): bool;


    public function exists(): bool;
}