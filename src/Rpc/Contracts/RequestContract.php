<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:27
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Contracts;

/**
 * Interface RequestContract
 * @package CrCms\Foundation\Rpc\Contracts
 */
interface RequestContract
{
    /**
     * @param string $name
     * @param array $params
     * @return ResponseContract
     */
    public function sendPayload(string $name, array $params = []): ResponseContract;
}