<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:14
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc;

use CrCms\Foundation\Rpc\Contracts\Call as CallContract;
use CrCms\Foundation\Rpc\Contracts\Request as RequestContract;

class Rpc implements CallContract
{
    protected $request;

    public function __construct(RequestContract $request)
    {
        $this->request = $request;
    }

    public function call(string $name, array $params = [])
    {
        return $this->request->sendPayload($name, $params = []);
    }


}