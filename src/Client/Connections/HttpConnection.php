<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:25
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client\Connections;

use CrCms\Foundation\Client\AbstractConnection;
use CrCms\Foundation\Client\Contracts\Connection;
use CrCms\Foundation\Client\Exceptions\ConnectionException;
use RuntimeException;

/**
 * Class HttpConnection
 * @package CrCms\Foundation\Client\Connections
 */
class HttpConnection extends AbstractConnection implements Connection
{
    /**
     * @var string
     */
    protected $method = 'post';

    /**
     * @var array
     */
    protected $payload = [];

    /**
     * @var string
     */
    protected $path;

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers): self
    {
        $this->connector->setHeaders($headers);
        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod(string $method): self
    {
        $this->method = strtolower($method);
        return $this;
    }

    /**
     * @param array $payload
     * @return $this
     */
    public function setPayload(array $payload): self
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @param string $path
     * @param array $data
     * @return Connection
     */
    public function send(string $path = '', array $data = []): Connection
    {
        $this->resolveSendPayload($path, $data);

        if (in_array($this->method, ['get', 'post'], true)) {
            $execResult = call_user_func_array([$this->connector, $this->method], [$this->path, json_encode($this->payload)]);
        } else {
            /* 这里需要详细测试，暂时此功能不可用 */
            $this->connector->setMethod($this->method);
            $this->connector->setData(json_encode($this->payload));
            $execResult = call_user_func_array([$this->connector, 'execute'], [$this->path]);
        }

        //加入异常连接
        if ($this->isAbnormalConnection(!$execResult)) {
            $this->connector->close();
            throw new ConnectionException($this);
        }

        $this->connector->close();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->connector->body ?? null;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->connector->statusCode;
    }

    /**
     * @param string $path
     * @param array $data
     */
    protected function resolveSendPayload(string $path, array $data)
    {
        if (!empty($data['method'])) {
            $this->setMethod($data['method']);
        } elseif (empty($this->method)) {
            $this->setMethod('post');
        }

        if (!empty($data['payload'])) {
            $this->setPayload($data['payload']);
        } elseif (empty($this->payload)) {
            $this->setPayload([]);
        }

        if (!empty($path)) {
            $this->setPath($path);
        }

        return;
    }

    /**
     * @param bool $isDead
     * @return bool
     */
    protected function isAbnormalConnection(bool $isDead = false): bool
    {
        if (in_array($this->connector->statusCode, [-1, -2], true) || $this->connector->errCode !== 0 || $isDead === true) {
            $this->markDead();
            return true;
        }

        return false;
    }
}