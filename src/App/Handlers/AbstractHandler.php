<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-07-17 20:40
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\App\Handlers;

use CrCms\Foundation\App\Handlers\Contracts\HandlerContract;
use CrCms\Foundation\App\Helpers\InstanceTrait;

/**
 * Class AbstractHandler
 * @package CrCms\Foundation\App\Actions
 */
abstract class AbstractHandler implements HandlerContract
{
    use InstanceTrait;
}