<?php

namespace CrCms\Foundation\Swoole\Process;

use Illuminate\Support\Collection;
use Swoole\Process;
use RuntimeException;

/**
 * Class ProcessManager
 * @package CrCms\Foundation\Swoole\Process
 */
class ProcessManager
{
    /**
     * @var Collection
     */
    protected $processes;

    /**
     * @var string
     */
    protected $file;

    /**
     * ProcessManager constructor.
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->file = $file;
        $this->processes = $this->resolveProcessesFromFile();
    }

    /**
     * @param AbstractProcess $process
     * @return bool
     */
    public function start(AbstractProcess $process, string $name = ''): bool
    {
        if ($process->start()) {
            $name = empty($name) ? $process->getName() : $name;
            if (empty($name)) {
                throw new RuntimeException("The process name is not empty");
            }
            $this->processes->put($name, ['pid' => $process->getPid()]);
            $this->processStoreToFile();
            return true;
        }

        return false;
    }

    /**
     * @param int $pid
     * @param int $signal
     * @return bool
     */
    public function kill(string $name, int $signal = SIGTERM): bool
    {
        if ($this->exists($name)) {
            $process = $this->processes->get($name);
            if (Process::kill($process['pid'], $signal)) {
                $this->processes->forget($name);
                $this->processStoreToFile();
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * @param int $pid
     * @return bool
     */
    public function exists(string $name): bool
    {
        if ($this->processes->has($name)) {
            $process = $this->processes->get($name);
            if (Process::kill($process['pid'], 0) === false) {
                $this->processes->forget($name);
                $this->processStoreToFile();
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->processes;
    }

    /**
     * @param string $file
     * @return int
     */
    protected function processStoreToFile(): int
    {
        return file_put_contents($this->file, $this->processes->toJson(), LOCK_EX);
    }

    /**
     * @param string $file
     * @return Collection
     */
    protected function resolveProcessesFromFile(): Collection
    {
        if (!file_exists($this->file) || !(bool)$content = file_get_contents($this->file)) {
            return new Collection([]);
        }

        return new Collection(json_decode($content, true));
    }
}