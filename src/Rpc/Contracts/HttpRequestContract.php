<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/07/02 19:27
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Contracts;

/**
 * Interface HttpRequestContract
 * @package CrCms\Foundation\Rpc\Contracts
 */
interface HttpRequestContract extends RequestContract
{
    /**
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * @return
     */
    public function getContent(): string;
}