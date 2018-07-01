<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/06/30 15:43
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client;

use BadMethodCallException;
use CrCms\Foundation\Client\Contracts\Connection;

/**
 * Class Client
 * @package CrCms\Foundation\Client
 */
class Client
{
    /**
     * @var \Illuminate\Foundation\Application|mixed
     */
    protected $manager;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * Client constructor.
     */
    public function __construct()
    {
        $this->manager = $this->manager();
        $this->connection = $this->connection();
    }

    /**
     * @param null|string $name
     * @return mixed
     */
    public function connection(?string $name = null)
    {
        return $this->manager->connection($name);
    }

    /**
     * @return \Illuminate\Foundation\Application|mixed
     */
    protected function manager()
    {
        return app('client.manager');
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->connection, $name)) {
            return call_user_func_array([$this->connection, $name], $arguments);
        }

        throw new BadMethodCallException("The method[{$name}] is not exists");
    }
}