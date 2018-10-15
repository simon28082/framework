<?php

namespace CrCms\Foundation\Swoole\Server\Events;

use CrCms\Foundation\Swoole\Server\AbstractServer;
use CrCms\Foundation\Swoole\Server\Contracts\EventContract;

/**
 * Class WorkerStartEvent
 * @package CrCms\Foundation\Swoole\Server\Events
 */
class WorkerStartEvent extends AbstractEvent implements EventContract
{
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
     * @param AbstractServer $server
     */
    public function handle(AbstractServer $server): void
    {
        parent::handle($server);

        parent::setEventProcessName(($this->server->taskworker ?
                'task_' :
                'worker_'
            ) . strval($this->workId));
    }
}