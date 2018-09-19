<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/07/04 11:14
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client\Http\Guzzle;

use CrCms\Foundation\ConnectionPool\AbstractConnection;
use CrCms\Foundation\ConnectionPool\Exceptions\ConnectionException;
use CrCms\Foundation\ConnectionPool\Contracts\Connection as ConnectionContract;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

/**
 * Class GuzzleHttpConnection
 * @package CrCms\Foundation\ConnectionPool\Connections
 */
class Connection extends AbstractConnection implements ConnectionContract
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
    public function send(string $uri, array $data = []): \CrCms\Foundation\ConnectionPool\Contracts\Connection
    {
//        $this->resolveSendPayload($uri, $data);
        //
        try {
            $this->response = $this->connector->request('get', $uri, [
                'json' => $data,
                'headers' => (array)$this->headers,
            ]);
        } catch (ConnectException $exception) {
//            $this->markDead();
            throw new ConnectionException($this, 'Connection failed: ' . $exception->getMessage());
        } catch (RequestException | ClientException $exception) {
            //400+可能是请求方法或参数错误，不可视为超时或服务器error
            $this->response = $exception->getResponse();

            throw new ConnectionException($this, 'Connection failed: ' . $exception->getMessage());
        }

        $this->connector->close();

        return $this;
    }


}