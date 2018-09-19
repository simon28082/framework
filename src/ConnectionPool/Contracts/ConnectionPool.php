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
    public function get(): Connection;

    /**
     * @param Connection $connection
     * @return void
     */
    public function put(Connection $connection): void;

    /**
     * @param Connection $connection
     * @return void
     */
    public function destroy(Connection $connection): void;

    /**
     * @param Connection $connection
     * @return void
     */
    public function release(Connection $connection): void;
}