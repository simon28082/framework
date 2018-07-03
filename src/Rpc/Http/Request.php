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
    protected $routePrefix;

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
        try {
            return $this->client->connection($this->client->getCurrentGroupName())->setHeaders($this->headers)
                ->setMethod('post')
                ->send($this->resolveName($name), ['payload' => $params]);
        } catch (ConnectionException $exception) {
            return $this->whileGetConnection($name, $params);
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