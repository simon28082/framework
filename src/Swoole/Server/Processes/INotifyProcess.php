<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/22 7:16
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\Server\Processes;


use Carbon\Carbon;
use CrCms\Foundation\Swoole\INotify;
use CrCms\Foundation\Swoole\Process\AbstractProcess;
use CrCms\Foundation\Swoole\Process\Contracts\ProcessContract;
use CrCms\Foundation\Swoole\Server\ProcessManage;
use CrCms\Foundation\Swoole\Traits\ProcessNameTrait;
use Swoole\Async;
use Swoole\Process;

/**
 * Class INotifyProcess
 * @package CrCms\Foundation\Swoole\Server\Processes
 */
class INotifyProcess extends AbstractProcess implements ProcessContract
{
    use ProcessNameTrait;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var ProcessManage
     */
    protected $processManage;

    /**
     * INotifyProcess constructor.
     * @param ProcessManage $processManage
     * @param array $config
     * @param bool $redirectStdinStdout
     * @param bool $createPipe
     */
    public function __construct(ProcessManage $processManage, array $config, bool $redirectStdinStdout = false, bool $createPipe = true)
    {
        $this->processManage = $processManage;
        $this->config = $config;
        parent::__construct($redirectStdinStdout, $createPipe);
    }

    /**
     * @param Process $process
     */
    public function handle(Process $process): void
    {
        $process->name($this->config['process_prefix'] . 'notify');

        $iNotify = new INotify($this->config['notify']['targets']);
        $iNotify->monitor(function ($events) {
            if (!empty($events) && $this->processManage->exists('servers')) {
                $this->processManage->kill(SIGUSR1, 'servers');
                Async::writeFile($this->config['notify']['log_path'], 'The notify process is reload' . Carbon::now()->toDateTimeString() . PHP_EOL, null, FILE_APPEND);
            } else {
                Async::writeFile($this->config['notify']['log_path'], 'no');
            }
        });
    }
}