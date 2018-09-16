<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:25
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\ConnectionPool\Contracts;

interface ConnectionPool
{
    public function has(): bool;

    public function next(): Connection;

    public function join(Connection $connection);

    public function release(Connection $connection);

    public function close(Connection $connection): void;
}