<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-07-17 20:40
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Framework\App\Handlers;

use CrCms\Framework\App\Handlers\Contracts\HandlerContract;
use CrCms\Framework\App\Helpers\InstanceConcern;

/**
 * Class AbstractHandler
 * @package CrCms\Framework\App\Actions
 */
abstract class AbstractHandler implements HandlerContract
{
    use InstanceConcern;
}