<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/4 6:05
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client\Selectors;

use CrCms\Foundation\Client\Contracts\Connection;
use CrCms\Foundation\Client\Contracts\ConnectionPool;
use CrCms\Foundation\Client\Contracts\Selector;

class ResidentSelector implements Selector
{
    /**
     * @param string $group
     * @param array $connections
     * @param ConnectionPool $pool
     * @return Connection
     */
    public function select(string $group, array $connections, ConnectionPool $pool): Connection
    {
        return $connections[0];
    }
}