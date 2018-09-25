<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:14
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Client;

use CrCms\Foundation\Client\Manager;
use CrCms\Foundation\ConnectionPool\Exceptions\ConnectionException;
use CrCms\Foundation\Rpc\Contracts\RpcContract;
use BadMethodCallException;
use CrCms\Foundation\Rpc\Contracts\ServiceDiscoverContract;

/**
 * Class Rpc
 * @package CrCms\Foundation\Rpc
 */
class Rpc
{
    /**
     * @var RpcContract
     */
    protected $rpc;

    /**
     * @var ServiceDiscoverContract
     */
    protected $serviceDiscover;

    /**
     * 重试次数
     *
     * @var int
     */
    protected $retry = 3;

    /**
     * @var Manager
     */
    protected $client;

    /**
     * Rpc constructor.
     */
    public function __construct(ServiceDiscoverContract $serviceDiscover, RpcContract $rpc)
    {
        $this->serviceDiscover = $serviceDiscover;
        $this->rpc = $rpc;
    }

    /**
     * @param string $name
     * @param null|string $uri
     * @param array $params
     * @return Manager
     */
    public function call(string $name, ?string $uri = null, array $params = []): Manager
    {
        $service = $this->serviceDiscover->discover($name);
        return $this->whileGetConnection($service, $uri, $params);
    }

    /**
     * 循环获取连接，直到非异常连接
     *
     * @param array $service
     * @param string $uri
     * @param array $params
     * @param int $depth
     * @return Manager
     */
    protected function whileGetConnection(array $service, string $uri, array $params = [], int $depth = 1): Manager
    {
        try {
            return $this->rpc->call($service, $uri, $params);
        } catch (ConnectionException $exception) {
            if ($depth > $this->retry) {
                throw $exception;
            }
            return $this->whileGetConnection($service, $uri, $params, $depth += 1);
        }
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
        /*if (method_exists($this->rpc, $name)) {
            $result = call_user_func_array([$this->rpc, $name], $arguments);
            if ($result instanceof RequestContract) {
                $this->request = $result;
                return $this;
            }

            return $result;
        }*/

        throw new BadMethodCallException("The method[{$name}] is not exists");
    }
}