<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/07/04 11:14
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client\Connections;

use CrCms\Foundation\Client\Contracts\Connection;
use CrCms\Foundation\Client\Exceptions\ConnectionException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;

/**
 * Class GuzzleHttpConnection
 * @package CrCms\Foundation\Client\Connections
 */
class GuzzleHttpConnection extends HttpConnection implements Connection
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->response->getBody()->getContents();
    }

    /**
     * @param string $path
     * @param array $data
     * @return Connection
     */
    public function send(string $path = '', array $data = []): Connection
    {
        $this->resolveSendPayload($path, $data);

        try {
            $this->response = $this->connector->request($this->method, $this->path, [
                'json' => $this->payload,
                'headers' => (array)$this->headers,
            ]);
        } catch (RequestException | ClientException $exception) {
            //400可能是请求方法或参数错误，不可视为超时或服务器error
            if ($exception->getCode() >= 500) {
                $this->markDead();
                throw new ConnectionException($this, 'Connection failed: ' . $exception->getMessage());
            }

            throw $exception;
        }

        return $this;
    }
}