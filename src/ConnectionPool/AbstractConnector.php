<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/1 18:54
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\ConnectionPool;

use CrCms\Foundation\ConnectionPool\Contracts\Connector;
use BadMethodCallException;
use InvalidArgumentException;

abstract class AbstractConnector implements Connector
{
    /**
     * @var Connector
     */
    protected $connect;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var int
     */
    protected $connectTime = 0;

    /**
     * @param $settings
     * @return array
     */
    protected function mergeSettings($settings): array
    {
        return array_merge($this->settings, $settings);
    }

    /**
     * @return mixed
     */
    public function getConnect()
    {
        return $this->connect;
    }

    /**
     * @return int
     */
    public function getConnectTime(): int
    {
        return $this->connectTime;
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