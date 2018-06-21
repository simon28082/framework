<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/20 7:16
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\Server;

use Illuminate\Support\Collection;
use Swoole\Process;
use RuntimeException;

/**
 * Class ProcessManage
 * @package CrCms\Foundation\Swoole\Server
 */
class ProcessManage
{
    /**
     * @var string
     */
    protected $file;

    /**
     * ProcessManage constructor.
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * @param Collection $pids
     * @return bool
     */
    public function store(Collection $pids): bool
    {
        return (bool)file_put_contents($this->file, $pids->toJson());
    }

    /**
     * @param int|string $pid
     * @return bool
     */
    public function exists($pid = -1): bool
    {
        $pids = $this->handlePids($pid);

        if ($pids->isEmpty()) {
            return false;
        }

        return $pids->map(function ($pid) {
            return Process::kill(intval($pid), 0);
        })->filter(function ($exists) {
            return !$exists;
        })->isEmpty();
    }

    /**
     * @param int $sign
     * @param int|string $pid
     * @return bool
     */
    public function kill(int $sign = SIGTERM, $pid = -1): bool
    {
        $this->handlePids($pid)->each(function ($pids) use ($sign) {
            return collect((array)$pids)->each(function ($pid) use ($sign) {
                return Process::kill(intval($pid), $sign);
            });
        });

        return true;
    }


    /**
     * @param $pid
     * @return bool
     */
    public function append($pid): bool
    {
        $pids = $this->all();

        if ($pid instanceof Collection) {
            $pids = $pids->merge($pid);
        } else {
            $pids->push($pid);
        }

        return $this->store($pids->unique());
    }

    /**
     * @return bool
     */
    public function clean(): bool
    {
        $result = @unlink($this->file);
        if (!$result) {
            throw new RuntimeException("Remove pid file : [{$this->config['pid_file']}] error");
        }
        return $result;
    }

    /**
     * @return Collection
     */
    protected function all(): Collection
    {
        if (file_exists($this->file) && filesize($this->file) > 0) {
            $result = file_get_contents($this->file);
            $result = json_decode($result, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException('JSON unserialize error , message:' . json_last_error_msg());
            }

            return collect($result);
        } else {
            return collect([]);
        }
    }

    /**
     * @param mixed $pid
     * @return Collection
     */
    protected function filter($pid): Collection
    {
        $pids = $this->all()->filter(function ($item, $key) use ($pid) {
            if (is_numeric($pid)) {
                return in_array($pid, (array)$item);
            } else {
                return $key == $pid;
            }
        });

        if ($pids->isEmpty()) {
            if (Process::kill(intval($pid), 0)) {
                return collect([$pid]);
            }
        }

        return $pids;
    }

    /**
     * @param $pid
     * @return Collection
     */
    protected function handlePids($pid): Collection
    {
        return $pid > 0 || !is_numeric($pid) ? $this->filter($pid) : $this->all();
    }
}