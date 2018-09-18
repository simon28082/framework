<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/26 6:13
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Client;

use CrCms\Foundation\ConnectionPool\AbstractConnectionFactory;
use CrCms\Foundation\ConnectionPool\Contracts\ConnectionPool as ConnectionPoolContract;
use CrCms\Foundation\ConnectionPool\Contracts\ConnectionFactory as ConnectionFactoryContract;
use CrCms\Foundation\ConnectionPool\Contracts\Connection as ConnectionContract;
use CrCms\Foundation\Rpc\Client\Http\Guzzle\Connection;
use CrCms\Foundation\Rpc\Client\Http\Guzzle\Connector;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

/**
 * Class Factory
 * @package CrCms\Foundation\Rpc\Client
 */
class Factory extends AbstractConnectionFactory implements ConnectionFactoryContract
{
    protected $driver;

    public function driver(string $driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @param array $config
     * @return ConnectionContract
     */
    public function make(ConnectionPoolContract $pool): ConnectionContract
    {
        return $this->createConnection($this->driver, $pool);
    }

    /**
     * @param string $driver
     * @param ConnectionPoolContract $pool
     * @return Connection
     */
    protected function createConnection(string $driver, ConnectionPoolContract $pool): Connection
    {
        switch ($driver) {
            case 'http':
                return new Connection($pool, $this->createConnector($driver), $this->configuration($driver));
        }

        throw new InvalidArgumentException("Unsupported driver [{$driver}]");
    }

    /**
     * @param string $driver
     * @return Connector
     */
    protected function createConnector(string $driver): Connector
    {
        switch ($driver) {
            case 'http':
                return new Connector();
        }

        throw new InvalidArgumentException("Unsupported driver [{$driver}]");
    }

    /**
     * @param string $name
     * @return array
     */
    protected function configuration(string $name): array
    {
        $connections = $this->app->make('config')->get('rpc.connections');

        if (!isset($connections[$name])) {
            throw new InvalidArgumentException("Rpc config[{$name}] not found");
        }

        return $connections[$name];
    }
}