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
     * 是否回收
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
    }

    protected function updateLaseActivityTime(): void
    {
        $this->lastActivityTime = time();
    }

    protected function increaseConnectionNumber(): void
    {
        $this->connectionNumber += 1;
    }

    public function getLaseActivityTime(): int
    {
        return $this->lastActivityTime;
    }

    public function isRelease(): bool
    {
        return $this->isRelease;
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
     * @return Connector
     */
    public function getConnector(): Connector
    {
        return $this->connector;
    }

    public function reconnection(): void
    {
        /* @todo 暂时 */
        //$this->connector->connect($this->config);
    }

    public function close(): void
    {
        $this->release();
    }

    public function release(): void
    {
        $this->isRelease = true;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return spl_object_hash($this);
    }

    public function request(string $uri, array $data = []): ConnectionContract
    {
        $this->updateLaseActivityTime();

        try {
            return $this->send($uri, $this->resolve($data));
        } catch (Exception $exception) {
            throw $exception;
        } finally {
            $this->connector->close();
        }
    }

    protected function resolve(array $data): array
    {
        return $data;
    }

    abstract protected function send(string $url, array $data): AbstractConnection;

    public function getResponse()
    {
        return $this->response;
    }

    public function getConnectionNumber(): int
    {
        return $this->connectionNumber;
    }

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