<?php

namespace CrCms\Foundation\Swoole\Process;

use SplObjectStorage;

/**
 * Class ProcessManager
 * @package CrCms\Foundation\Swoole\Process
 */
class ProcessManager
{

    protected $processes;

    protected $pids;

    protected static $instance;

    public function __construct()
    {
        $this->processes = new SplObjectStorage;
//        $this->pids = new \ArrayObject();
    }

    public function start(AbstractProcess $process): int
    {
        /*if (array_key_exists($process, $this->processes)) {

        }*/

        $this->processes->attach($process);
        return $process->start();
    }

    public function kill(AbstractProcess $process)
    {
        Process::kill(intval($this->processes->offsetGet($process)->getPid()), SIGTERM);
    }

    public function list(): SplObjectStorage
    {
        return $this->processes;
    }

    protected function exists(string $process)
    {

    }

    public static function instance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __call(string $name, array $arguments)
    {
        $process = array_unshift($arguments);
        if ($this->processes->contains($process)) {
            return $process->{$name(...$arguments)};
        }

        throw new BadMethodCallException("The method[{$name}] not exists");
    }
}