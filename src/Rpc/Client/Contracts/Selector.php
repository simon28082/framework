<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:31
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Client\Contracts;

interface Selector
{

    public function select(array $connections);

    public function position(): int;

}