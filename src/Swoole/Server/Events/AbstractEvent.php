<?php

namespace CrCms\Foundation\Swoole\Server\Events;

use CrCms\Foundation\Swoole\Server\AbstractServer;
use CrCms\Foundation\Swoole\Server\Contracts\EventContract;
use CrCms\Foundation\Swoole\Traits\ProcessNameTrait;

/**
 * Class AbstractEvent
 * @package CrCms\Foundation\Swoole\Server\Events
 */
abstract class AbstractEvent implements EventContract
{
    use ProcessNameTrait;

    /**
     * @var AbstractServer
     */
    protected $server;

    /**
     * @param AbstractServer $server
     */
    public function handle(AbstractServer $server): void
    {
        $this->server = $server;
    }

    /**
     * @return AbstractServer
     */
    public function getServer(): AbstractServer
    {
        return $this->server;
    }

    /**
     * @param string $processName
     */
    protected function setEventProcessName(string $processName)
    {
        $processPrefix = config('swoole.process_prefix', 'swoole') . ($this->server->getName() ? '_' . $this->server->getName() : '');

        $processName = ($processPrefix ? $processPrefix . '_' : $processPrefix) . $processName;

        static::setProcessName($processName);
    }
}