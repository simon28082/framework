<?php

namespace CrCms\Foundation\ConnectionPool\Contracts;

/**
 * Interface ConnectionFactory
 * @package CrCms\Foundation\ConnectionPool\Contracts
 */
interface ConnectionFactory
{
    /**
     * @param ConnectionPool $pool
     * @return Connection
     */
    public function make(ConnectionPool $pool): Connection;
}