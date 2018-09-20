<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:14
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client;

use CrCms\Foundation\Application;
use CrCms\Foundation\ConnectionPool\ConnectionManager;
use Illuminate\Contracts\Config\Repository;
use InvalidArgumentException;
use BadMethodCallException;
use Crcms\Foundation\ConnectionPool\Contracts\Connection;

/**
 *
 *
 * Class Manager
 * @package CrCms\Foundation\Client
 */
class Manager
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var ConnectionManager
     */
    protected $manager;

    /**
     * @var Connection
     */
    protected $connection;

    protected $factory;

    /**
     * Manager constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->manager = $this->app->make('pool.manager');
        $this->factory = $this->app->make('client.factory');
    }

    /**
     * @param null|string $name
     * @return $this
     */
    public function connection(?string $name = null)
    {
        $name = $name ? $name : $this->defaultDriver();

        $this->connection = $this->manager->connection(
            $this->factory->config($this->configuration($name))
            , $this->poolName()
        );

//        ConnectionManager:: 应该为ConnectionPoolMnager，负责分发调度创建连接的操作，最后返回一个Connection即可
//        ConnectionPool只用于存储，取出Connection，配置的验证逻辑应该在ConnectionPoolMnager里面
//        Client通过获取ConnectionPoolMnager里面的Connection来执行操作，不应该操作ConnectionPoolMnager
//        通过manager来吐出连接

        return $this;
    }

    public function getFactory()
    {
        return $this->factory;
    }

    public function close()
    {
        $this->manager->close($this->connection);
        $this->connection = null;
    }

    /**
     * @return ConnectionManager
     */
    public function getManager(): ConnectionManager
    {
        return $this->manager;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    protected function poolName(): string
    {
        return $this->app->make('config')->get('client.pool');
    }

    /**
     * @return string
     */
    protected function defaultDriver(): string
    {
        return $this->app->make('config')->get('client.default');
    }

    /**
     * @param string $name
     * @return array
     */
    protected function configuration(string $name): array
    {
        $connections = $this->app->make('config')->get('client.connections');

        if (!isset($connections[$name])) {
            throw new InvalidArgumentException("Client config[{$name}] not found");
        }

        return $connections[$name];
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        //接手控制权
        if ($this->connection instanceof Connection) {
            $result = call_user_func_array([$this->connection, $name], $arguments);
            if ($result instanceof Connection) {
                $this->connection = $result;
                return $this;
            }

            return $result;
        }

        throw new BadMethodCallException("The method[{$name}] is not exists");
    }
}