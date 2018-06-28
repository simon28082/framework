<?php

/**
 * @author simon <434730525@qq.com>
 * @datetime 2018-06-28 10:36
 * @link http://www.koodpower.com/
 * @copyright Copyright &copy; 2018 Rights Reserved 快点动力
 */

namespace CrCms\Foundation\Rpc\Client;

use CrCms\Foundation\Rpc\Client\Contracts\Connection;
use CrCms\Foundation\Rpc\Client\Contracts\ConnectionPool;
use Illuminate\Foundation\Application;
use InvalidArgumentException;

/**
 * Class ConnectionManager
 * @package CrCms\Foundation\Rpc\Client
 */
class ConnectionManager
{
    /**
     * @var ConnectionFactory
     */
    protected $factory;

    /**
     * @var ConnectionPool
     */
    protected $pool;

    /**
     * @var Application
     */
    protected $app;

    /**
     * ConnectionManager constructor.
     * @param Application $app
     * @param ConnectionFactory $factory
     * @param ConnectionPool $pool
     */
    public function __construct(Application $app, ConnectionFactory $factory, ConnectionPool $pool)
    {
        $this->app = $app;
        $this->pool = $pool;
        $this->factory = $factory;
    }

    /**
     * @param null|string $name
     * @return Connection
     */
    public function connction(?string $name = null): Connection
    {
        $name = $name ? $name : $this->defaultDriver();

        if ($this->pool->hasConnection($name)) {
            return $this->pool->nextConnection($name);
        }

        return $this->pool->setConnections(
            $name, $this->makeConnections($name)
        )->nextConnection($name);
    }

    /**
     * @param string $name
     * @return array
     */
    protected function makeConnections(string $name)
    {
        return array_map(function ($config) use ($name) {
            return $this->factory->make($name, $config);
        }, $this->configuration($name));
    }

    /**
     * @return string
     */
    protected function defaultDriver(): string
    {
        return $this->app->make('config')->get('rpc.default');
    }

    /**
     * @param string $name
     * @return array
     */
    protected function configuration(string $name): array
    {
        $connections = $this->app->make('config')->get('rpc.connections');

        if (!isset($connections[$name])) {
            throw new InvalidArgumentException("rpc config[{$name}] not found");
        }

        return $connections[$name];
    }
}