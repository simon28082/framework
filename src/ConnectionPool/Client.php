<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/06/30 15:43
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\ConnectionPool;

use BadMethodCallException;
use CrCms\Foundation\ConnectionPool\Contracts\Connection;

/**
 * Class Client
 * @package CrCms\Foundation\ConnectionPool
 */
class Client
{
    /**
     * @var ConnectionManager
     */
    protected $manager;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $currentGroupName;

    /**
     * Client constructor.
     * @param null|string $name
     */
    public function __construct(?string $name = null)
    {
        $this->manager();
        $this->connection($name);
    }

    /**
     * @param null|string $name
     * @return $this
     */
    public function connection(?string $name = null)
    {
        $this->setCurrentGroupName($name);
        $this->connection = $this->manager->connection($name);
        return $this;
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @param null|string $name
     * @return $this
     */
    protected function setCurrentGroupName(?string $name = null)
    {
        $this->currentGroupName = $name;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getCurrentGroupName(): ?string
    {
        return $this->currentGroupName;
    }

    /**
     * @return void
     */
    protected function manager()
    {
        $this->manager = app('client.manager');
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->connection, $name)) {
            return call_user_func_array([$this->connection, $name], $arguments);
        }

        throw new BadMethodCallException("The method[{$name}] is not exists");
    }
}