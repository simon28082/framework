<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-07-14 09:39
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\App\Actions;

/**
 * Trait HandlerTrait
 * @package CrCms\Foundation\App\Actions
 */
trait HandlerTrait
{
    /**
     * @var array
     */
    protected $defaults = [];

    /**
     * @param array $data
     * @return void
     */
    abstract protected function resolveDefaults(array $data): void;
}