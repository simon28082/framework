<?php

/**
 * @author simon <simon@crcms.cn>
 * @datetime 2018-08-15 07:03
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Sso\Client\Contracts;

use CrCms\Foundation\Rpc\Contracts\ResponseContract;

/**
 * Interface InteractionContract
 * @package CrCms\Foundation\Sso\Client\Contracts
 */
interface InteractionContract
{
    /**
     * @param string $token
     * @return object
     */
    public function refresh(string $token): object;

    /**
     * @param string $token
     * @return object
     */
    public function user(string $token): object;

    /**
     * @param string $token
     * @return bool
     */
    public function check(string $token): bool;

    /**
     * @param string $token
     * @return bool
     */
    public function logout(string $token): bool;
}