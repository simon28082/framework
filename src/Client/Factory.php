<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/26 6:13
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client;

use CrCms\Foundation\ConnectionPool\AbstractConnectionFactory;
use CrCms\Foundation\ConnectionPool\Contracts\ConnectionPool as ConnectionPoolContract;
use CrCms\Foundation\ConnectionPool\Contracts\ConnectionFactory as ConnectionFactoryContract;
use CrCms\Foundation\ConnectionPool\Contracts\Connection as ConnectionContract;
use CrCms\Foundation\Client\Http\Guzzle\Connection;
use CrCms\Foundation\Client\Http\Guzzle\Connector;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

/**
 * Class Factory
 * @package CrCms\Foundation\Rpc\Client
 */
class Factory extends AbstractConnectionFactory implements ConnectionFactoryContract
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @param array $config
     * @return ConnectionFactoryContract
     */
    public function config(array $config): ConnectionFactoryContract
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return ConnectionContract
     */
    public function make(): ConnectionContract
    {
        return $this->createConnection($this->config);
    }

    /**
     * @param string $driver
     * @return Connection
     */
    protected function createConnection(array $config): Connection
    {
        switch ($config['driver']) {
            case 'http':
                return new Connection($this->createConnector($config)->connect($config), $config);
        }

        throw new InvalidArgumentException("Unsupported driver [{$config['driver']}]");
    }

    /**
     * @param string $driver
     * @return Connector
     */
    protected function createConnector(array $config): Connector
    {
        switch ($config['driver']) {
            case 'http':
                return new Connector;
        }

        throw new InvalidArgumentException("Unsupported driver [{$config['driver']}]");
    }
}