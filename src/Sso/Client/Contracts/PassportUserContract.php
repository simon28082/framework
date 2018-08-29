<?php

/**
 * @author simon <simon@crcms.cn>
 * @datetime 2018-08-30 07:10
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Sso\Client\Contracts;

/**
 * Interface PassportUserContract
 * @package CrCms\Foundation\Sso\Client\Contracts
 */
interface PassportUserContract
{
    /**
     * @param array $attributes
     * @return PassportUserContract
     */
    public function getPassportUser(array $attributes): PassportUserContract;
}