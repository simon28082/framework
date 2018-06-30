<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:25
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client\Contracts;

/**
 * Interface ConnectionPool
 * @package CrCms\Foundation\Client\Contracts
 */
interface ConnectionPool
{
    /**
     * @param string $group
     * @return bool
     */
    public function hasConnection(string $group): bool;

    /**
     * @param string $group
     * @return Connection
     */
    public function nextConnection(string $group): Connection;

    /**
     * @param string $group
     * @return ConnectionPool
     */
    public function deathConnection(string $group): ConnectionPool;

    /**
     * @param string $group
     * @param array $connections
     * @return ConnectionPool
     */
    public function setConnections(string $group, array $connections): ConnectionPool;

    /**
     * @param string $group
     * @param Connection $connection
     * @return ConnectionPool
     */
    public function addConnection(string $group, Connection $connection): ConnectionPool;

    /**
     * @return array
     */
    public function getAllConnections(): array;
}