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
use InvalidArgumentException;
use BadMethodCallException;

/**
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
     * @var
     */
    protected $connectionPool;

    /**
     * Manager constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param null|string $name
     * @return $this
     */
    public function connection(?string $name = null)
    {
        $name = $name ? $name : $this->defaultDriver();

        $factory = $this->app->make('client.factory')->config($this->configuration($name));

        $this->connectionPool = $this->app->make('pool.manager')->connection($factory, $this->poolName());

        return $this;
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
        //让渡控制权
        if ($this->connectionPool instanceof ConnectionManager) {
            return call_user_func_array([$this->connectionPool, $name], $arguments);
        }

        throw new BadMethodCallException("The method[{$name}] is not exists");
    }
}