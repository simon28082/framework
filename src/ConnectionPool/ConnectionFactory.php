<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/26 6:13
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\ConnectionPool;

use CrCms\Foundation\ConnectionPool\Connections\GuzzleHttpConnection;
use CrCms\Foundation\ConnectionPool\Connections\HttpConnection;
use CrCms\Foundation\ConnectionPool\Connections\SocketConnection;
use CrCms\Foundation\ConnectionPool\Connectors\GuzzleHttpConnector;
use CrCms\Foundation\ConnectionPool\Connectors\HttpConnector;
use CrCms\Foundation\ConnectionPool\Connectors\SocketConnector;
use CrCms\Foundation\ConnectionPool\Contracts\Connection;
use CrCms\Foundation\ConnectionPool\Contracts\Connector;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use CrCms\Foundation\ConnectionPool\Contracts\ConnectionPool as ConnectionPoolContract;

/**
 * Class ConnectionFactory
 * @package CrCms\Foundation\ConnectionPool
 */
class ConnectionFactory
{
    /**
     * ConnectionFactory constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
    }

    /**
     * @param array $config
     * @return Connection
     */
    public function make(array $config, ConnectionPool $pool): Connection
    {
        return $this->createConnection($config, $pool);
    }

    /**
     * @param array $config
     * @return Connector
     */
    protected function createConnector(array $config): Connector
    {
        switch ($config['driver']) {
            case 'socket':
                return new SocketConnector();
            case 'http':
                return new HttpConnector();
            case 'guzzle_http':
                return new GuzzleHttpConnector();
        }

        throw new InvalidArgumentException("Unsupported driver [{$config['driver']}]");
    }

    /**
     * @param array $config
     * @return Connection
     */
    protected function createConnection(array $config, ConnectionPool $pool): Connection
    {
        $connect = $this->createConnector($config)->connect($config);

        switch ($config['driver']) {
            case 'socket':
                return new SocketConnection($pool, $connect, $config);
            case 'http':
                return new HttpConnection($pool, $connect, $config);
            case 'guzzle_http':
                return new GuzzleHttpConnection($pool, $connect, $config);
        }

        throw new InvalidArgumentException("Unsupported driver [{$config['driver']}]");
    }
}