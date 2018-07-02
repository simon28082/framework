<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:14
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc;

use CrCms\Foundation\Rpc\Contracts\RequestContract;
use CrCms\Foundation\Rpc\Contracts\ResponseContract;
use CrCms\Foundation\Rpc\Contracts\RpcContract;

class Rpc implements RpcContract
{
    /**
     * @param string $name
     * @param array $params
     * @return ResponseContract
     */
    public function call(string $name, array $params = []): ResponseContract
    {
        return app(RequestContract::class)->sendPayload($name, $params = []);
    }

    public function authentication(string $key, string $passowrd = ''): RpcContract
    {
    }
}