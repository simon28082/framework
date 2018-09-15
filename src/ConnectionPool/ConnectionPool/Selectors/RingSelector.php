<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/1 21:35
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\ConnectionPool\Selectors;

use CrCms\Foundation\ConnectionPool\Contracts\Connection;
use CrCms\Foundation\ConnectionPool\Contracts\ConnectionPool;
use CrCms\Foundation\ConnectionPool\Contracts\Selector;

/**
 * Class RingSelector
 * @package CrCms\Foundation\ConnectionPool\Selectors
 */
class RingSelector implements Selector
{
    /**
     * @var array
     */
    protected $groups;

    /**
     * @param string $group
     * @param array $connections
     * @param ConnectionPool $pool
     * @return Connection
     */
    public function select(string $group, array $connections, ConnectionPool $pool): Connection
    {
        $pointer = $this->groups[$group] ?? -1;
        $allNum = count($connections);

        $pointer += 1;
        if ($pointer >= $allNum || $allNum === 1) {
            $pointer = 0;
        }

        $this->groups[$group] = $pointer;

        return $connections[$pointer];
    }
}