<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-04-02 20:58
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\Events;

use CrCms\Foundation\Swoole\Server;
use CrCms\Foundation\Swoole\Traits\ProcessNameTrait;

/**
 * Class ManagerStartEvent
 * @package CrCms\Foundation\Swoole\Events
 */
class ManagerStartEvent extends AbstractEvent implements EventContract
{
    use ProcessNameTrait;

    public function handle(Server $server): void
    {
        parent::handle($server);
        static::setProcessName($this->server->getConfig()['process_prefix'].'manage');
    }
}