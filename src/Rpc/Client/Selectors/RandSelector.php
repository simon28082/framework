<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:47
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Client\Selectors;

use CrCms\Foundation\Rpc\Contracts\Selector;

/**
 * Class RandSelector
 * @package CrCms\Foundation\Rpc\Client\Selectors
 */
class RandSelector implements Selector
{
    /**
     * @param array $services
     * @return array
     */
    public function select(array $services): array
    {
        return $services[array_rand($services)];
    }
}