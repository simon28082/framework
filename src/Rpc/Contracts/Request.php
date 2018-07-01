<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:27
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Contracts;


interface Request
{

    public function sendPayload(string $name, array $params = []): Response;

}