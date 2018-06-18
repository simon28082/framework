<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/18 18:09
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\Server;

use CrCms\Foundation\Swoole\Server\Contracts\ProcessContract;
use Swoole\Process;

class ProcessManage implements ProcessContract
{

    protected $process;

    protected $callback;

    protected $name;

    public function __construct(Process $process)
    {
        $this->process = $process;
    }

//    protected function createProcess()
//    {
//        $this->process = new Process($this->callback);
//        $this->process->name($this->name);
//    }

    public function start(): bool
    {
        return $this->process->start();
    }

    public function exists(): bool
    {
        return Process::kill($this->process->pid, 0);
    }

    public function stop(): bool
    {
        return Process::kill($this->process->pid);
    }
}