<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/1 18:54
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client;

use CrCms\Foundation\Client\Contracts\Connector;
use BadMethodCallException;
use InvalidArgumentException;

abstract class AbstractConnector implements Connector
{
    /**
     * @var array
     */
    protected $defaultSettings = [
        'timeout' => 1
    ];

    /**
     * @var Connector
     */
    protected $connect;

    /**
     * @param $settings
     * @return array
     */
    protected function mergeSettings($settings): array
    {
        return array_merge($this->defaultSettings, $settings);
    }

    /**
     * @return mixed
     */
    public function resource()
    {
        return $this->connect;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if (property_exists($this->connect, $name)) {
            return $this->connect->{$name};
        }

        throw new InvalidArgumentException("The attribute[{$name}] is not exists");
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