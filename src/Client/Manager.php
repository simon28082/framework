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
use CrCms\Foundation\ConnectionPool\Contracts\ConnectionFactory;
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

    /**
     * @var ConnectionFactory
     */
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

        return $this;
    }

    /**
     * @return ConnectionFactory
     */
    public function getFactory(): ConnectionFactory
    {
        return $this->factory;
    }

    /**
     * @return void
     */
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

    /**
     * @return Connection
     */
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