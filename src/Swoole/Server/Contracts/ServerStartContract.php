<?php

namespace CrCms\Foundation\Swoole\Server\Contracts;

use CrCms\Foundation\Swoole\Process\ProcessManager;
use Illuminate\Console\Command;

/**
 * Interface ServerStartContract
 * @package CrCms\Foundation\Swoole\Server\Contracts
 */
interface ServerStartContract
{
    /**
     * @param Command $command
     * @param ServerContract $server
     * @param ProcessManager $process
     */
    public function run(Command $command, ServerContract $server, ProcessManager $process): void;
}