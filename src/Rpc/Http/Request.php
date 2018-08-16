<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:20
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Http;

use CrCms\Foundation\Client\Client;
use CrCms\Foundation\Client\Contracts\Connection;
use CrCms\Foundation\Client\Exceptions\ConnectionException;
use CrCms\Foundation\Rpc\Contracts\RequestContract;
use CrCms\Foundation\Rpc\Contracts\HttpRequestContract;
use CrCms\Foundation\Rpc\Contracts\ResponseContract;
use Exception;

/**
 * Class Request
 * @package CrCms\Foundation\Rpc\Http
 */
class Request implements RequestContract, HttpRequestContract
{
    /**
     * @var array
     */
    protected $headers = [
        'User-Agent' => 'CRCMS-JSON-RPC PHP Client',
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $routePrefix = '';

    /**
     * Request constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        //只做到这，这个有问题
        $this->connection = $this->connection();
    }

    /**
     * @param string $prefix
     * @return $this
     */
    public function setRoutePrefix(string $prefix)
    {
        $this->routePrefix = $prefix;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->connection->getStatusCode();
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->connection->getContent();
    }

    /**
     * @param string $name
     * @param array $params
     * @return ResponseContract
     */
    public function sendPayload(string $name, array $params = []): ResponseContract
    {
        $this->connection = $this->whileGetConnection($name, $params);

        return app(ResponseContract::class)->parse($this);
    }

    /**
     * 循环获取连接，直到非异常连接
     *
     * @param string $name
     * @param array $params
     * @return Connection
     */
    protected function whileGetConnection(string $name, array $params = []): Connection
    {
        // 这个方法还是有问题的
        // 最好增加一个循环次数，因为connection如果全部丢掉还会自动创建
        // 然后一直连不上再创建，就会陷入死循环
        // 最好的方法是判断当前的connection还剩余多少个，超过这个次数就退出

        try {
            return $this->client->connection($this->client->getCurrentGroupName())->setHeaders($this->headers)
                ->setMethod('post')
                ->send($this->resolveName($name), ['payload' => $params]);
        } catch (ConnectionException $exception) {
            if ($exception->getConnection()->getStatusCode() >= 500) {
                return $this->whileGetConnection($name, $params);
            }

            throw $exception;
        }
    }

    protected function connection(): Client
    {
        return $this->client->connection($this->client->getCurrentGroupName())->getConnection();
    }


    /**
     * @param string $name
     * @return string
     */
    protected function resolveName(string $name): string
    {
        return str_replace('.', '/', $this->routePrefix . '.' . $name);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->connection, $name)) {
            return call_user_func_array([$this->connection, $name], $arguments);
        }

        throw new BadMethodCallException("The method[{$name}] is not exists");
    }
}