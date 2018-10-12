<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/19 21:00
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\Process;

use CrCms\Foundation\Swoole\Process\Contracts\ProcessContract;
use Swoole\Process;
use BadMethodCallException;

/**
 * Class AbstractProcess
 * @package CrCms\Foundation\Swoole\Process
 */
abstract class AbstractProcess implements ProcessContract
{
    /**
     * @var Process
     */
    protected $process;

    /**
     * 子进程名称
     *
     * @var string
     */
    protected $name;

    /**
     * AbstractProcess constructor.
     * @param bool $redirectStdinStdout
     * @param int $createPipe
     */
    public function __construct(bool $redirectStdinStdout = false, int $createPipe = 0)
    {
        $this->process = new Process([$this, 'initChildProcess'], $redirectStdinStdout, $createPipe);
    }

    /**
     * @param Process $process
     * @return mixed
     */
    public function initChildProcess(Process $process)
    {
        if (!empty($this->name)) {
            swoole_set_process_name($this->name);
        }

        return call_user_func([$this, 'childProcess'], $process);
    }

    /**
     * @return bool
     */
    public function start(): bool
    {
        return (bool)$this->process->start();
    }

    /**
     * @return bool
     */
    public function exit(): bool
    {
        $this->process->exit(0);
        return true;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name ?? '';
    }

    /**
     * @return int
     */
    public function getPid(): int
    {
        return $this->process->pid;
    }

    /**
     * @return Process
     */
    public function getProcess(): Process
    {
        return $this->process;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->process, $name)) {
            return $this->process->{$name(...$arguments)};
        }

        throw new BadMethodCallException("The method[{$name}] not exists");
    }
}