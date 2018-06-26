<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/17 16:31
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\Socket;

use BadMethodCallException;
use Swoole\Client as SwooleClient;

/**
 * Class Client
 * @package CrCms\Foundation\Swoole\Socket
 */
class Client
{
    /**
     * @var \Swoole\Client
     */
    protected $client;

    /**
     * Client constructor.
     * @param SwooleClient $client
     */
    public function __construct(SwooleClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->client, $name)) {
            return call_user_func_array([$this->client, $name], $arguments);
        }

        throw new BadMethodCallException("The method [{$name}] is not exists");
    }
}