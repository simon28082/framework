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

/**
 * @property RequestContract method
 * @property RequestContract headers
 *
 * Class Rpc
 * @package CrCms\Foundation\Rpc
 */
class Rpc implements RpcContract
{
    /**
     * @var RequestContract
     */
    protected $request;

    /**
     * Rpc constructor.
     */
    public function __construct()
    {
        $this->request = app(RequestContract::class);
    }

    /**
     * @param string $name
     * @param array $params
     * @return ResponseContract
     */
    public function call(string $name, array $params = []): ResponseContract
    {
        return $this->request->sendPayload($name, $params);
    }

    /**
     * @param string $key
     * @param string $passowrd
     * @return RpcContract
     */
    public function authentication(string $key, string $passowrd = ''): RpcContract
    {
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->request, $name)) {
            $result = call_user_func_array([$this->request, $name], $arguments);
            if ($result instanceof RequestContract) {
                $this->request = $result;
                return $this;
            }

            return $result;
        }

        throw new BadMethodCallException("The method[{$name}] is not exists");
    }
}