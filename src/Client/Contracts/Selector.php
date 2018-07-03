<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:31
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client\Contracts;

interface Selector
{
    /**
     * @param string $group
     * @param array $connections
     * @param ConnectionPool $pool
     * @return Connection
     */
    public function select(string $group, array $connections, ConnectionPool $pool): Connection;
}