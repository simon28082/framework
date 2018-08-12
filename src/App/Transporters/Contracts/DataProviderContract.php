<?php

/**
 * @author simon <simon@crcms.cn>
 * @datetime 2018-08-12 11:00
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Transporters\Contracts;

/**
 * Interface DataProviderContract
 * @package CrCms\Foundation\Transporters\Contracts
 */
interface DataProviderContract
{
    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * @return array
     */
    public function all(): array;

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * @param array $keys
     * @return array
     */
    public function only(array $keys): array;

    /**
     * @param array $keys
     * @return array
     */
    public function except(array $keys): array;

    /**
     * @param string $key
     * @param $value
     * @return void
     */
    public function set(string $key, $value): void;

    /**
     * @param string $key
     * @return void
     */
    public function remove(string $key): void;
}