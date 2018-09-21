<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:14
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Client;

use CrCms\Foundation\Rpc\Contracts\RequestContract;
use CrCms\Foundation\Rpc\Contracts\ResponseContract;
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
     * Rpc constructor.
     */
    public function __construct(ServiceDiscoverContract $serviceDiscover, RpcContract $rpc)
    {
        $this->serviceDiscover = $serviceDiscover;
        $this->rpc = $rpc;
    }

    /**
     * @param string $name
     * @param array $params
     * @return ResponseContract
     */
    public function call(string $name, ?string $uri, array $params = [])
    {
        $service = $this->serviceDiscover->discover($name);
        $client = $this->rpc->call($service, $uri, $params);
        dd($client->getContent());
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
        if (method_exists($this->rpc, $name)) {
            $result = call_user_func_array([$this->rpc, $name], $arguments);
            if ($result instanceof RequestContract) {
                $this->request = $result;
                return $this;
            }

            return $result;
        }

        throw new BadMethodCallException("The method[{$name}] is not exists");
    }
}