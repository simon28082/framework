<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/26 6:13
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Client;

use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

class ConnectionFactory
{

    public function __construct(Container $container)
    {
    }

    public function make(string $name, array $config)
    {
        $config = $this->parseConfig($name);

        $this->createConnection($config);
    }

    protected function parseConfig(string $name, array $connections)
    {
        return $connections[$name];
    }

    protected function createConnector(array $config)
    {
        switch ($config['driver']) {
            case 'socket':
                return new SocketConnector;
        }

        throw new InvalidArgumentException("Unsupported driver [{$config['driver']}]");
    }

    protected function createConnection(array $config)
    {
        $this->createConnector($config)->connect($config);
    }
}