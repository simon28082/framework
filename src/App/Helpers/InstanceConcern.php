<?php

/**
 * @author simon <simon@crcms.cn>
 * @datetime 2018-08-12 13:55
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\App\Helpers;

use CrCms\Foundation\Helpers\Concerns\InstanceConcern as BaseInstanceConcern;

/**
 * @property-read Container $app
 * @property-read Config $config
 * @property-read Cache $cache
 * @property-read AuthFactory $auth
 * @property-read Dispatcher $dispatcher
 * @property-read Guard $guard
 * @property-read DataProviderContract $data
 * @property-read AbstractServer|Server|\Swoole\Http\Server|\Swoole\WebSocket\Server $server
 *
 * Trait InstanceConcern
 * @package CrCms\Foundation\App\Helpers
 */
trait InstanceConcern
{
    use BaseInstanceConcern;
}