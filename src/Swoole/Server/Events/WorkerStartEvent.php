<?php

namespace CrCms\Foundation\Swoole\Server\Events;

use CrCms\Foundation\Swoole\Server\AbstractServer;
use CrCms\Foundation\Swoole\Traits\ProcessNameTrait;
use CrCms\Foundation\Swoole\Server\Contracts\EventContract;

/**
 * Class WorkerStartEvent
 * @package CrCms\Foundation\Swoole\Server\Events
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
     * @param AbstractServer $server
     */
    public function handle(AbstractServer $server): void
    {
        parent::handle($server);

        $this->setWorkOrTaskProcessName();
    }

    /**
     *
     */
    protected function setWorkOrTaskProcessName(): void
    {
        $processPrefix = config('swoole.process_prefix');

        $processName = (
            $this->server->taskworker ?
                $processPrefix . 'task_' :
                $processPrefix . 'worker_'
            ) . strval($this->workId);

        static::setProcessName($processName);
    }
}