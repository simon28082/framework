<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-04-02 20:58
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\Server\Events;

use CrCms\Foundation\Swoole\Server\AbstractServer;
use CrCms\Foundation\Swoole\Traits\ProcessNameTrait;
use CrCms\Foundation\Swoole\Server\Contracts\EventContract;

/**
 * Class ManagerStartEvent
 * @package CrCms\Foundation\Swoole\Events
 */
class ManagerStartEvent extends AbstractEvent implements EventContract
{
    use ProcessNameTrait;

    public function handle(AbstractServer $server): void
    {
        parent::handle($server);
//        static::setProcessName($this->server->getConfig()['process_prefix'].'manage');
    }
}