<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/1 21:35
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Client\Selectors;

use CrCms\Foundation\Rpc\Contracts\Selector;

/**
 * Class RingSelector
 * @package CrCms\Foundation\Rpc\Client\Selectors
 */
class RingSelector implements Selector
{
    /**
     * @var int
     */
    protected $pointer = 0;

    /**
     * @param array $connections
     * @return array
     */
    public function select(array $connections): array
    {
        $allNum = count($connections);

        if ($this->pointer >= $allNum || $allNum === 1) {
            $this->pointer = 0;
        }

        return $connections[$this->pointer];
    }
}