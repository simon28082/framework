<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:14
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Client;

use function CrCms\Foundation\App\Helpers\is_serialized;
use CrCms\Foundation\Client\Manager;
use CrCms\Foundation\ConnectionPool\Exceptions\ConnectionException;
use CrCms\Foundation\Rpc\Contracts\RpcContract;
use BadMethodCallException;
use CrCms\Foundation\Rpc\Contracts\ServiceDiscoverContract;
use stdClass;
use InvalidArgumentException;

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
     * @var object
     */
    protected $data;

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
     * @return object
     */
    public function call(string $name, ?string $uri = null, array $params = [])
    {
        $service = $this->serviceDiscover->discover($name, 'consul');
        $this->client = $this->whileGetConnection($service, $uri, $params);

        $this->resolveData($this->client->getContent());

        return $this->getData();
    }

    /**
     * @param mixed $data
     */
    protected function resolveData($data): void
    {
        if (is_array($data)) {
            $this->data = (object)$data;
        } else if ((bool)($newData = json_decode($data)) && json_last_error() === 0) {
            $this->data = $newData;
        } else if (is_serialized($data)) {
            $this->data = unserialize($data);
        } else {
            $this->data = new stdClass();
            $this->data->data = $data;
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
     * @return object
     */
    public function getData(): object
    {
        return $this->data;
    }

    /**
     * @return Manager
     */
    public function getClient(): Manager
    {
        return $this->client;
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
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if (isset($this->data->{$name})) {
            return $this->data->{$name};
        }

        throw new InvalidArgumentException("The attribute[{$name}] is not exists");
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