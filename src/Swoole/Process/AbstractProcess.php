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
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $pid;

    /**
     * AbstractProcess constructor.
     * @param array $params
     * @param bool $redirectStdinStdout
     * @param bool $createPipe
     */
    public function __construct(bool $redirectStdinStdout = false, bool $createPipe = true)
    {
        $this->process = new Process([$this, 'handle'], $redirectStdinStdout, $createPipe);
        $this->process->name($this->name);
    }

    /**
     * @return int
     */
    public function start(): int
    {
        $this->pid = $this->process->start();
        return $this->pid;
    }

    /**
     * @return int
     */
    public function exit(): int
    {
        return $this->process->exit();
    }

    /**
     * @return int
     */
    public function getPid(): int
    {
        return $this->pid;
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