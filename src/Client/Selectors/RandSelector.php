<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:47
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client\Selectors;

use CrCms\Foundation\Client\Contracts\Connection;
use CrCms\Foundation\Client\Contracts\ConnectionPool;
use CrCms\Foundation\Client\Contracts\Selector;
use InvalidArgumentException;

class RandSelector implements Selector
{
    /**
     * @param string $group
     * @param array $connections
     * @param ConnectionPool $pool
     * @return Connection
     */
    public function select(string $group, array $connections, ConnectionPool $pool): Connection
    {
        return $connections[array_rand($connections)];
    }
}