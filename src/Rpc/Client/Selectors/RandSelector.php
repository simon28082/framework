<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:47
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Client\Selectors;

use CrCms\Foundation\Rpc\Client\Connection;
use CrCms\Foundation\Rpc\Client\Contracts\Selector;

class RandSelector implements Selector
{

    protected $position;

    public function select(array $connections): Connection
    {
        $key = array_rand($connections);
        $this->position = $key;
        return $connections[$key];
    }

    public function position(): int
    {
        return $this->position;
    }


}