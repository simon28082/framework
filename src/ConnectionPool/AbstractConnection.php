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
use CrCms\Foundation\ConnectionPool\Contracts\ConnectionPool as ConnectionPoolContract;

/**
 * Class AbstractConnection
 * @package CrCms\Foundation\ConnectionPool
 */
abstract class AbstractConnection implements ConnectionContract
{
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
     * 连接时间
     *
     * @var int
     */
    protected $connectionTime = 0;

    /**
     * 连接次数
     *
     * @var int
     */
    protected $connectionNumber = 0;

    /**
     * 连接失败次数
     *
     * @var int
     */
    protected $connectionFailureNumber = 0;

    /**
     * 连接失败时间
     *
     * @var int
     */
    protected $connectionFailureTime = 0;

    /**
     * 超时时间
     * 
     * @var int 
     */
    protected $timeout = 10;

    /**
     * AbstractConnection constructor.
     * @param Connector $connector
     * @param array $config
     */
    public function __construct(Connector $connector, array $config = [])
    {
        $this->connector = $connector;
        $this->config = $config;
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
        $this->connectionTime = time();
        $this->connectionNumber += 1;

        return $this;
    }

    /**
     * @return ConnectionContract
     */
    public function markDead(): ConnectionContract
    {
        $this->isAlive = false;
        $this->connectionFailureNumber += 1;
        $this->connectionFailureTime = time();

        return $this;
    }

    /**
     * @return Connector
     */
    public function getConnector(): Connector
    {
        return $this->connector;
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