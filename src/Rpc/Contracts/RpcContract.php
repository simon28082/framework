<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/23 18:36
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Contracts;

interface RpcContract
{
    /**
     * @param string $name
     * @param array $params
     * @return ResponseContract
     */
    public function call(string $name, array $params = []): ResponseContract;

    /**
     * @param string $key
     * @param string $passowrd
     * @return RpcContract
     */
    public function authentication(string $key, string $passowrd = ''): RpcContract;
}