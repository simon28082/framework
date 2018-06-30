<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/06/30 15:43
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client;

/**
 * Class Client
 * @package CrCms\Foundation\Client
 */
class Client
{
    protected $manager;

    protected $connection;

    public function __construct()
    {
        $this->manager = $this->manager();
        $this->connection = $this->connection();
    }

    public function connection(?string $name = null)
    {
        return $this->manager->connection($name);
    }

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