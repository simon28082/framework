<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/26 6:13
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Client;

use CrCms\Foundation\Rpc\Client\Connections\SocketConnection;
use CrCms\Foundation\Rpc\Client\Connectors\SocketConnector;
use CrCms\Foundation\Rpc\Client\Contracts\Connection;
use CrCms\Foundation\Rpc\Client\Contracts\Connector;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;
use InvalidArgumentException;

/**
 * Class ConnectionFactory
 * @package CrCms\Foundation\Rpc\Client
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
     * @param string $name
     * @param array $config
     * @return Connection
     */
    public function make(string $name, array $config): Connection
    {
        return $this->createConnection($config);
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
        }

        throw new InvalidArgumentException("Unsupported driver [{$config['driver']}]");
    }

    /**
     * @param array $config
     * @return Connection
     */
    protected function createConnection(array $config): Connection
    {
        $connect = $this->createConnector($config)->connect($config);

        switch ($config['driver']) {
            case 'socket':
                return new SocketConnection($connect, $config);
        }

        throw new InvalidArgumentException("Unsupported driver [{$config['driver']}]");
    }
}