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
use CrCms\Foundation\Rpc\Contracts\ResponseContract;
use Exception;

/**
 * Class Request
 * @package CrCms\Foundation\Rpc\Http
 */
class Request implements RequestContract
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
    protected $method = 'post';

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
     * @param array $headers
     * @return $this
     */
    public function headers(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function method(string $method)
    {
        $this->method = $method;
        return $this;
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

        return app(ResponseContract::class)->parse($this->connection);
    }

    /**
     * 循环获取连接，直到非异常连接
     *
     * @param string $name
     * @param array $params
     * @return Connection
     */
    protected function whileGetConnection(string $group, string $name, array $params = [], int $depth = 1): Connection
    {
        try {
            return $this->client->connection($group)->setMethod($this->method)
                ->setHeaders($this->headers)
                ->send($name, $params);
        } catch (ConnectionException $exception) {
            $statusCode = $exception->getConnection()->getStatusCode();

            /* @todo 暂时只用depth来进行限制，更好的就统计当前的组连接数 */
            if ($depth > 3 || ($statusCode <= 500 && $statusCode > 0)) {
                throw $exception;
            }

            return $this->whileGetConnection($group, $name, $params, $depth += 1);
        }
    }

    /**
     * @param string $name
     * @return string
     */
    protected function resolveName(string $name): string
    {
        return str_replace('.', '/', $this->routePrefix . '.' . $name);
    }
}