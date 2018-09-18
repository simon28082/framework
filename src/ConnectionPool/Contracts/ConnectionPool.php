<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:25
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\ConnectionPool\Contracts;

/**
 * Interface ConnectionPool
 * @package CrCms\Foundation\ConnectionPool\Contracts
 */
interface ConnectionPool
{
    /**
     * @return bool
     */
    public function has(): bool;

    /**
     * @return Connection
     */
    public function connection(): Connection;

    /**
     * @param Connection $connection
     * @return ConnectionPool
     */
    public function join(Connection $connection): ConnectionPool;

    /**
     * @param Connection $connection
     */
    public function release(Connection $connection): void;

    /**
     * @param Connection $connection
     */
    public function close(Connection $connection): void;
}