<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-04-02 20:52
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\Traits;

/**
 * Trait ProcessNameTrait
 * @package CrCms\Foundation\Swoole\Traits
 */
trait ProcessNameTrait
{
    /**
     * @param string $name
     * @return void
     */
    protected static function setProcessName(string $name)
    {
        if (function_exists('cli_set_process_title')) {
            return cli_set_process_title($name);
        } elseif (function_exists('swoole_set_process_name')) {
            return swoole_set_process_name($name);
        }
    }
}