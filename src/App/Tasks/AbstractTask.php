<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-07-17 20:40
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Framework\App\Tasks;

use CrCms\Framework\App\Helpers\InstanceConcern;
use CrCms\Framework\App\Tasks\Contracts\TaskContract;

/**
 * Class AbstractTask
 * @package CrCms\Framework\App\Tasks
 */
abstract class AbstractTask implements TaskContract
{
    use InstanceConcern;

}