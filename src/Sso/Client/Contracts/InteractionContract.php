<?php

/**
 * @author simon <simon@crcms.cn>
 * @datetime 2018-08-15 07:03
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Sso\Client\Contracts;

/**
 * Interface InteractionContract
 * @package CrCms\Foundation\Sso\Client\Contracts
 */
interface InteractionContract
{
    /**
     * @param string $token
     * @return array
     */
    public function refresh(string $token): array;

    /**
     * @param string $token
     * @return array
     */
    public function user(string $token): array;

    /**
     * @param string $token
     * @return bool
     */
    public function check(string $token): bool;
}