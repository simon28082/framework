<?php

/**
 * @author simon <434730525@qq.com>
 * @datetime 2018-06-28 10:39
 * @link http://www.koodpower.com/
 * @copyright Copyright &copy; 2018 Rights Reserved 快点动力
 */

namespace CrCms\Foundation\Rpc\Client;

use CrCms\Foundation\Rpc\Client\Contracts\Call;

/**
 * Class Client
 * @package CrCms\Foundation\Rpc\Client
 */
class Client implements Call
{
    protected $connectionManage;

    protected $connection;

    public function __construct(ConnectionManage $connectionManage)
    {
        $this->connectionManage = $connectionManage;
    }

    public function connection(string $name)
    {
        $this->connection = $this->connectionManage->getConnectionPool()->nextConnection($name);
    }

    public function call(string $name, array $params = [])
    {
        //这里再包装一个response响应体
        $data = $this->connection->send($name, $params);
        return new Response($data);
    }


}

$client = new \Client();

$client->call('x.y', ['a' => 1, 'b' => 2]);