<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/26 6:25
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\ConnectionPool\Contracts;

/**
 * Interface Connector
 * @package CrCms\Foundation\ConnectionPool\Contracts
 */
interface Connector
{
    /**
     * @param array $config
     * @return Connector
     */
    public function connect(array $config): Connector;

    /**
     * @return mixed
     */
    public function resource();

    /**
     * @return void
     */
    public function close(): void;
}