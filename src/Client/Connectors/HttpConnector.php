<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/26 20:42
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client\Connectors;

use CrCms\Foundation\Client\Contracts\Connector;
use Swoole\Coroutine\Http\Client;
use BadMethodCallException;

/**
 * Class HttpConnector
 * @package CrCms\Foundation\Client\Connectors
 */
class HttpConnector implements Connector
{
    /**
     * @var array
     */
    protected $defaultSettings = [
        'timeout' => 1
    ];

    /**
     * @var Client
     */
    protected $connect;

    /**
     * @param array $config
     * @return Client
     */
    public function connect(array $config)
    {
        $this->connect = new Client($config['host'], $config['port']);
        $this->connect->set(array_merge($this->defaultSettings, $config['settings'] ?? []));
        return $this->connect;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->connect, $name)) {
            return call_user_func_array([$this->connect, $name], $arguments);
        }

        throw new BadMethodCallException("The method[{$name}] is not exists");
    }
}