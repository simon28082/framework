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
     * @return ConnectionFactory
     */
    public function config(array $config): ConnectionFactory;

    /**
     * @return Connection
     */
    public function make(): Connection;
}