<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:46
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client;

use CrCms\Foundation\Client\Contracts\Connection as ConnectionContract;
use CrCms\Foundation\Client\Contracts\Connector;
use CrCms\Foundation\Client\Contracts\ConnectionPool as ConnectionPoolContract;

/**
 * Class AbstractConnection
 * @package CrCms\Foundation\Client
 */
abstract class AbstractConnection implements ConnectionContract
{
    /**
     * @var bool
     */
    protected $isAlive = true;

    /**
     * @var Connector
     */
    protected $connector;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var int
     */
    protected $connectionFailureTime = 0;

    /**
     * @var int
     */
    protected $connectionFailureNum = 0;

    /**
     * @var mixed
     */
    protected $connctorResource;

    /**
     * AbstractConnection constructor.
     * @param Connector $connector
     * @param array $config
     */
    public function __construct(Connector $connector, array $config = [])
    {
        $this->connector = $connector;
        $this->connctorResource = $connector->resource();
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
        $this->connectionFailureNum = 0;
        $this->connectionFailureTime = 0;

        return $this;
    }

    /**
     * @return ConnectionContract
     */
    public function markDead(): ConnectionContract
    {
        $this->isAlive = false;
        $this->connectionFailureNum += 1;
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