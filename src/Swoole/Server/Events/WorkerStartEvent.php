<?php

namespace CrCms\Foundation\Swoole\Server\Events;

use CrCms\Foundation\Swoole\Server\AbstractServer;
use CrCms\Foundation\Swoole\Traits\ProcessNameTrait;
use CrCms\Foundation\Swoole\Server\Contracts\EventContract;

/**
 * Class WorkStartEvent
 * @package CrCms\Foundation\Swoole\Events
 */
class WorkerStartEvent extends AbstractEvent implements EventContract
{
    use ProcessNameTrait;

    /**
     * @var int
     */
    protected $workId;

    /**
     * WorkerStartEvent constructor.
     * @param int $workId
     */
    public function __construct(int $workId)
    {
        $this->workId = $workId;
    }

    /**
     * @param Server $server
     */
    public function handle(AbstractServer $server): void
    {
        parent::handle($server);

//        $this->setWorkOrTaskProcessName();
    }

    /**
     * @return void
     */
    protected function setWorkOrTaskProcessName(): void
    {
        $processPrefix = $this->server->getConfig()['process_prefix'];
        if ($this->swooleServer->taskworker) {
            static::setProcessName($processPrefix.'task');
        } else {
            static::setProcessName($processPrefix.'worker');
        }
    }
}