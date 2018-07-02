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
use CrCms\Foundation\Rpc\Contracts\RequestContract;
use CrCms\Foundation\Rpc\Contracts\HttpRequestContract;
use CrCms\Foundation\Rpc\Contracts\ResponseContract;
use RuntimeException;
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
     * Request constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
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
        try {
            $this->connection = $this->client->connection('http')->setHeaders($this->headers)
                ->setMethod('post')
                ->send($name, ['payload' => $params]);
        } catch (Exception $exception) {
            throw new RuntimeException($exception->getMessage());
        }

        return app(ResponseContract::class)->parse($this);
    }
}