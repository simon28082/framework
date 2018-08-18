<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:14
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Contracts;

use CrCms\Foundation\Client\Contracts\Connection;

/**
 * Interface ResponseContract
 * @package CrCms\Foundation\Rpc\Contracts
 */
interface ResponseContract
{
    /**
     * @param Connection $connection
     * @return ResponseContract
     */
    public function parse(Connection $connection): ResponseContract;

    /**
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * @return string|array|object
     */
    public function getContent();

    /**
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function data(string $key, $default = null);
}