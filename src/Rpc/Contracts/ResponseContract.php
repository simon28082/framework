<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:14
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Contracts;

interface ResponseContract
{
    /**
     * @param RequestContract $request
     * @return ResponseContract
     */
    public function parse(RequestContract $request): ResponseContract;
}