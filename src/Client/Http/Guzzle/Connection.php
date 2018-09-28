<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/07/04 11:14
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client\Http\Guzzle;

use CrCms\Foundation\Client\Http\Contracts\ResponseContract;
use CrCms\Foundation\ConnectionPool\AbstractConnection;
use CrCms\Foundation\ConnectionPool\Exceptions\ConnectionException;
use CrCms\Foundation\ConnectionPool\Contracts\Connection as ConnectionContract;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use CrCms\Foundation\ConnectionPool\Exceptions\RequestException as ConnectionPoolRequestException;

/**
 * Class GuzzleHttpConnection
 * @package CrCms\Foundation\ConnectionPool\Connections
 */
class Connection extends AbstractConnection implements ConnectionContract, ResponseContract
{
    /**
     * @var string
     */
    protected $method = 'post';

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod(string $method)
    {
        $this->method = strtolower($method);

        return $this;
    }

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
     * @param string $path
     * @param array $data
     */
    protected function resolve(array $data): array
    {
        if (!empty($data['method'])) {
            $this->setMethod($data['method']);
            unset($data['method']);
        }

        if (!empty($data['headers'])) {
            $this->setHeaders($data['headers']);
            unset($data['headers']);
        }

        return $data['payload'] ?? [];
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->response ? $this->response->getStatusCode() : -1;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->response ? $this->response->getBody()->getContents() : null;
    }

    /**
     * @param string $uri
     * @param array $data
     * @return ConnectionContract
     */
    public function send(string $uri, array $data = []): AbstractConnection
    {
        $options = [];
        $options['headers'] = $this->headers;

        if (isset($this->headers['Content-Type']) && $this->headers['Content-Type'] === 'application/json') {
            $options['json'] = $data;
        } else if ($this->method === 'get') {
            $options['query'] = $data;
        } else if (isset($this->headers['Content-Type']) && $this->headers['Content-Type'] === 'application/x-www-form-urlencoded') {
            $options['form_params'] = $data;
        } else {
            $options['body'] = $data;
        }

        try {
            $this->response = $this->connector->request($this->method, $uri, $options);
        } catch (ConnectException $exception) {
            throw new ConnectionException($this, 'Connection failed: ' . $exception->getMessage());
        } catch (RequestException | ClientException $exception) {
            //400+可能是请求方法或参数错误，不可视为超时或服务器error
            $this->response = $exception->getResponse();
            throw new ConnectionPoolRequestException($this, 'Request failed: ' . $exception->getMessage());
        } finally {
            $this->connector->close();
        }

        return $this;
    }
}