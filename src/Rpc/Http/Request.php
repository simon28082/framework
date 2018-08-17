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
        $group = strstr($name, '.', true);
        $name = $this->resolveName(substr(strstr($name, '.'), 1));
        $params = ['payload' => $params];

        $this->connection = $this->whileGetConnection($group, $name, $params);

        return app(ResponseContract::class)->parse($this);
    }

    /**
     * 循环获取连接，直到非异常连接
     *
     * @param string $name
     * @param array $params
     * @return Connection
     */
    protected function whileGetConnection(string $group, string $name, array $params = []): Connection
    {
        // 这个方法还是有问题的
        // 最好增加一个循环次数，因为connection如果全部丢掉还会自动创建
        // 然后一直连不上再创建，就会陷入死循环
        // 最好的方法是判断当前的connection还剩余多少个，超过这个次数就退出

        try {
            return $this->client->connection($group)->setHeaders($this->headers)
                ->setMethod('get')
                ->send($name, $params);
        } catch (ConnectionException $exception) {
            $statusCode = $exception->getConnection()->getStatusCode();
            if ($statusCode >= 500 || $statusCode <= 0) {
                return $this->whileGetConnection($group, $name, $params);
            }

            throw $exception;
        }
    }

    /**
     * 每次调用都是一个新的connection连接
     *
     * @return Client
     */
//    protected function client(): Client
//    {
//        $this->client = $this->client->connection($group);
//        return $this->client;
//    }

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
//    public function __call(string $name, array $arguments)
//    {
//        return call_user_func_array([$this->client, $name], $arguments);
//    }
}