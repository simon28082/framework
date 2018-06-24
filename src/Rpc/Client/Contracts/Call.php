<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/23 18:36
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Client\Contracts;

interface Call
{
    /**
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function call(string $name, array $params = []);
}