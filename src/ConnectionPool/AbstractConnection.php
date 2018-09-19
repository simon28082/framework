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
use CrCms\Foundation\ConnectionPool\Contracts\ConnectionPool;

/**
 * Class AbstractConnection
 * @package CrCms\Foundation\ConnectionPool
 */
abstract class AbstractConnection implements ConnectionContract
{
    /**
     * @var ConnectionPool
     */
    protected $pool;

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
     * 连接次数
     *
     * @var int
     */
    protected $connectionSuccessfulNumber = 0;

    /**
     * 连接失败次数
     *
     * @var int
     */
    protected $connectionFailureNumber = 0;

    /**
     * 最后连接时间
     *
     * @var int
     */
    protected $connectionLastTime = 0;

    /**
     * AbstractConnection constructor.
     * @param Connector $connector
     * @param array $config
     */
    public function __construct(ConnectionPool $pool, Connector $connector, array $config = [])
    {
        $this->pool = $pool;
        $this->connector = $connector;
        $this->config = $config;
        $this->updateConnectionTime();
    }

    public function updateConnectionTime(): void
    {
        $this->connectionLastTime = time();
    }

    public function getConnectionTime(): int
    {
        return $this->connectionLastTime;
    }

    /**
     * @return bool
     */
    public function isAlive(): bool
    {
        return $this->isAlive;
    }

    /**
     * @return ConnectionContract
     */
    public function makeAlive(): ConnectionContract
    {
        $this->isAlive = true;

        return $this;
    }

    /**
     * @return ConnectionContract
     */
    public function markDead(): ConnectionContract
    {
        $this->isAlive = false;

        return $this;
    }

    /**
     * @return int
     */
    public function getConnectionLastTime(): int
    {
        return $this->connectionLastTime;
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
        $this->connector->close();
        $this->pool->release($this);
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return spl_object_hash($this);
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