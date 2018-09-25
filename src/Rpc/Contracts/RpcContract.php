<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/23 18:36
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Contracts;

use CrCms\Foundation\Client\Manager;

interface RpcContract
{
    /**
     * @param array $service
     * @param string $uri
     * @param array $params
     * @return Manager
     */
    public function call(array $service, string $uri, array $params = []): Manager;

    /**
     * @param string $key
     * @param string $passowrd
     * @return RpcContract
     */
    public function authentication(string $key, string $passowrd = ''): RpcContract;
}