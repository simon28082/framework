<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-04-06 19:35
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\App\Helpers\Hash\Contracts;

/**
 * Interface HashVerify
 * @package CrCms\App\Helpers\Hash\Contracts
 */
interface HashVerify
{
    /**
     * @return bool
     */
    public function check(array $values, string $hash): bool;

    /**
     * @param array $values
     * @return string
     */
    public function make(array $values): string;
}