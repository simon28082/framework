<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:47
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\ConnectionPool\Selectors;

use CrCms\Foundation\ConnectionPool\Contracts\Connection;
use CrCms\Foundation\ConnectionPool\Contracts\ConnectionPool;
use CrCms\Foundation\ConnectionPool\Contracts\Selector;
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