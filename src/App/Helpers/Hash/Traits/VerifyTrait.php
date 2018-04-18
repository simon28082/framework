<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-04-06 19:40
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\App\Helpers\Hash\Traits;

use Illuminate\Support\Facades\Hash;

/**
 * Trait VerifyTrait
 * @package CrCms\App\Helpers\Hash\Traits
 */
trait VerifyTrait
{
    /**
     * @param array $values
     * @param string $hash
     * @return bool
     */
    public function check(array $values, string $hash): bool
    {
        return Hash::check($this->combination($values), $hash);
    }

    /**
     * @param array $values
     * @return string
     */
    public function make(array $values): string
    {
        return Hash::make($this->combination($values));
    }

    /**
     * @param array $values
     * @return string
     */
    abstract protected function combination(array $values): string ;
}