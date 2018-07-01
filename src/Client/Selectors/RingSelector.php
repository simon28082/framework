<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/1 21:35
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client\Selectors;

use CrCms\Foundation\Client\Contracts\Connection;
use CrCms\Foundation\Client\Contracts\Selector;

/**
 * Class RingSelector
 * @package CrCms\Foundation\Client\Selectors
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
     * @return Connection
     */
    public function select(string $group, array $connections): Connection
    {
        $pointer = $this->groups[$group] ?? -1;
        $allNum = count($connections);

        $pointer += 1;
        if ($pointer > $allNum || $allNum === 1) {
            $pointer = 0;
        }

        $this->groups[$group] = $pointer;

        return $connections[$pointer];
    }
}