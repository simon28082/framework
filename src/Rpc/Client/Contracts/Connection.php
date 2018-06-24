<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:33
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Client\Contracts;


interface Connection
{

    public function isAlive(): bool;

    public function makeAlivie(): bool;

    public function markDead(): bool;

    public function position(): bool;

}