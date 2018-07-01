<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:33
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client\Contracts;


/**
 * Interface Connection
 * @package CrCms\Foundation\Client\Contracts
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
     * @return mixed
     */
    public function send(string $path = '', array $data = []);

    /**
     * @return mixed
     */
//    public function recv(array $options = []);
}