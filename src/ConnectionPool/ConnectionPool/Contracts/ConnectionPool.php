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
     * @param string $group
     * @return bool
     */
    public function has(string $group): bool;

    /**
     * @param string $group
     * @return Connection
     */
    public function next(string $group): Connection;

    /**
     * @param string $group
     * @param array $connections
     * @return ConnectionPool
     */
    public function create(string $group, array $connections): ConnectionPool;

    /**
     * @param string $group
     * @return array
     */
    public function group(string $group): array;

    /**
     * @param string $group
     * @param Connection $connection
     */
    public function close(string $group, Connection $connection): void;
}