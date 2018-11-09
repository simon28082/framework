<?php

/**
 * @author simon <simon@crcms.cn>
 * @datetime 2018-11-09 19:47
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\MicroService\Contracts;

/**
 * Interface ResponseContract
 * @package CrCms\Foundation\MicroService\Contracts
 */
interface ResponseContract
{
    /**
     * @return void
     */
    public function send();
}