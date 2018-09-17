<?php

namespace CrCms\Foundation\ConnectionPool\Contracts;

/**
 * Interface ConnectionFactory
 * @package CrCms\Foundation\ConnectionPool\Contracts
 */
interface ConnectionFactory
{
    /**
     * @param array $config
     * @param ConnectionPool $pool
     * @return Connection
     */
    public function make(array $config, ConnectionPool $pool): Connection;
}