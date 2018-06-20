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
     * AbstractProcess constructor.
     * @param array $params
     * @param bool $redirectStdinStdout
     * @param bool $createPipe
     */
    public function __construct(bool $redirectStdinStdout = false, bool $createPipe = true)
    {
        $this->process = new Process([$this, 'handle'], $redirectStdinStdout, $createPipe);
    }

    /**
     * @return int
     */
    public function start(): int
    {
        return $this->process->start();
    }

    /**
     * @return Process
     */
    public function getProcess(): Process
    {
        return $this->process;
    }
}