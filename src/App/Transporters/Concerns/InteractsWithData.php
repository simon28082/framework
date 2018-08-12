<?php

/**
 * @author simon <simon@crcms.cn>
 * @datetime 2018-08-12 11:26
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Transporters\Concerns;

use Illuminate\Support\Arr;

/**
 * Trait InteractsWithData
 * @package CrCms\Foundation\Transporters\Concerns
 */
trait InteractsWithData
{
    /**
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return $this->has($key);
    }

    /**
     * @param array $keys
     * @return bool
     */
    public function hasAny(array $keys)
    {
        $input = $this->all();

        foreach ($keys as $key) {
            if (Arr::has($input, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function input(string $key, $default = null)
    {
        return $this->get($key, $default);
    }
}