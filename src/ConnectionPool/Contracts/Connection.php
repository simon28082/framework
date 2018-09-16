<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:33
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\ConnectionPool\Contracts;


/**
 * Interface Connection
 * @package CrCms\Foundation\ConnectionPool\Contracts
 */
interface Connection
{
    /**
     * @return bool
     */
    public function isAlive(): bool;

    /**
     * @return Connection
     */
    public function makeAlive(): Connection;

    /**
     * @return Connection
     */
    public function markDead(): Connection;

    /**
     * @param string $path
     * @param array $data
     * @return Connection
     */
    public function send(string $path = '', array $data = []): Connection;

    /**
     * @return mixed
     */
    public function getContent();

    /**
     * @return void
     */
    public function close();

    public function reconnection();
}