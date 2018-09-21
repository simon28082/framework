<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:46
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\ConnectionPool;

use CrCms\Foundation\ConnectionPool\Contracts\Connection as ConnectionContract;
use CrCms\Foundation\ConnectionPool\Contracts\Connector;
use BadMethodCallException;
use Exception;

/**
 * Class AbstractConnection
 * @package CrCms\Foundation\ConnectionPool
 */
abstract class AbstractConnection implements ConnectionContract
{
    /**
     * @var mixed
     */
    protected $response;

    /**
     * @var Connector
     */
    protected $connector;

    /**
     * @var array
     */
    protected $config;

    /**
     * 是否是存活链接
     *
     * @var bool
     */
    protected $isAlive = true;

    /**
     * 是否释放资源
     *
     * @var bool
     */
    protected $isRelease = false;

    /**
     * 最后活动时间
     *
     * @var int
     */
    protected $lastActivityTime = 0;

    /**
     * 连接次数
     *
     * @var int
     */
    protected $connectionNumber = 0;

    /**
     * AbstractConnection constructor.
     * @param array $config
     */
    public function __construct(Connector $connector, array $config = [])
    {
        $this->connector = $connector;
        $this->config = $config;
        $this->updateLaseActivityTime();
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return spl_object_hash($this);
    }

    /**
     * @return int
     */
    public function getLaseActivityTime(): int
    {
        return $this->lastActivityTime;
    }

    /**
     * @return int
     */
    public function getConnectionNumber(): int
    {
        return $this->connectionNumber;
    }

    /**
     * @return bool
     */
    public function isAlive(): bool
    {
        return $this->isAlive;
    }

    /**
     * @return void
     */
    public function makeAlive(): void
    {
        $this->isAlive = true;
    }

    /**
     * @return void
     */
    public function markDead(): void
    {
        $this->isAlive = false;
    }

    /**
     * @return bool
     */
    public function isRelease(): bool
    {
        return $this->isRelease;
    }

    /**
     * 释放连接
     *
     * @return void
     */
    public function makeRelease(): void
    {
        $this->isRelease = true;
    }

    /**
     * 回收连接
     *
     * @return void
     */
    public function makeRecycling(): void
    {
        $this->isRelease = false;
    }

    /**
     * @return void
     */
    protected function updateLaseActivityTime(): void
    {
        $this->lastActivityTime = time();
    }

    /**
     * @return void
     */
    protected function increaseConnectionNumber(): void
    {
        $this->connectionNumber += 1;
    }

    /**
     * @return Connector
     */
    public function getConnector(): Connector
    {
        return $this->connector;
    }

    /**
     * @return void
     */
    public function reconnection(): void
    {
        // @todo
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param string $uri
     * @param array $data
     * @return ConnectionContract
     * @throws Exception
     */
    public function request(string $uri, array $data = []): ConnectionContract
    {
        $this->updateLaseActivityTime();
        $this->increaseConnectionNumber();

        try {
            return $this->send($uri, $this->resolve($data));
        } catch (Exception $exception) {
            throw $exception;
        } finally {
            $this->connector->close();
        }
    }

    /**
     * @param array $data
     * @return array
     */
    protected function resolve(array $data): array
    {
        return $data;
    }

    /**
     * @param string $url
     * @param array $data
     * @return AbstractConnection
     */
    abstract protected function send(string $url, array $data): AbstractConnection;

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->connector, $name)) {
            return call_user_func_array([$this->connector, $name], $arguments);
        }

        throw new BadMethodCallException("The method[{$name}] is not exists");
    }
}