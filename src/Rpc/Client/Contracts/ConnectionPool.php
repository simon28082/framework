<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:25
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Client\Contracts;

interface ConnectionPool
{

    public function nextConnection(): Connection;


    public function deathConnection(Connection $connection);

}