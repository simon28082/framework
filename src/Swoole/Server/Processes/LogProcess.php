<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/19 21:10
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\Server\Processes;

use CrCms\Foundation\Swoole\Process\AbstractProcess;
use CrCms\Foundation\Swoole\Process\Contracts\ProcessContract;
use Illuminate\Support\Collection;
use Swoole\Process;

class LogProcess extends AbstractProcess implements ProcessContract
{
    /**
     * @var Collection
     */
    protected $processes;

    /**
     * @var string
     */
    protected $logPath;

    /**
     * LogProcess constructor.
     * @param Collection $processes
     * @param string $logPath
     * @param bool $redirectStdinStdout
     * @param bool $createPipe
     */
    public function __construct(Collection $processes, string $logPath, bool $redirectStdinStdout = false, bool $createPipe = true)
    {
        $this->processes = $processes;
        $this->logPath = $logPath;
        parent::__construct($redirectStdinStdout, $createPipe);
    }

    /**
     * @param Process $process
     */
    public function handle(Process $process): void
    {
        $process->name('swoole_log');

        $this->processes->each(function (ServerProcess $serverProcess, $key) {
            swoole_event_add($serverProcess->getProcess()->pipe, function () use ($serverProcess) {
                swoole_async_write($this->logPath, $serverProcess->getProcess()->read());
            });
        });
    }
}