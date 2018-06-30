<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:47
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client\Selectors;

use CrCms\Foundation\Client\Contracts\Connection;
use CrCms\Foundation\Client\Contracts\Selector;

class RandSelector implements Selector
{
    /**
     * @param array $connections
     * @return Connection
     */
    public function select(array $connections): Connection
    {
        $key = array_rand($connections);
        return $connections[$key];
    }
}