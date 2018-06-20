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

class ProcessManage
{

    protected $file;

    /**
     * @var Collection
     */
    protected $pids;

    public function __construct(string $file)
    {
        $this->file = $file;
        $this->pids = collect([]);
    }

    public function store(Collection $pids): bool
    {
        return (bool)file_put_contents($this->file, $pids->implode(','));
    }

    public function exists(int $pid = -1): bool
    {
        $pids = $pid > 0 ? $this->filter($pid) : $this->all();

        return $pids->map(function ($pid) {
            return Process::kill(intval($pid), 0);
        })->filter(function ($exists) {
            return !$exists;
        })->isEmpty();
    }

    public function kill(int $pid = -1): bool
    {
        $pids = $pid > 0 ? $this->filter($pid) : $this->all();
        $pids->each(function ($pid) {
            return Process::kill(intval($pid), SIGTERM);
        });
        return true;
    }

    public function append($pid): bool
    {
        if ($pid instanceof Collection) {
            $this->pids = $this->pids->merge($pid);
        } else {
            $this->pids->push($pid);
        }

        return $this->store($this->pids->unique());
    }

    protected function all(): Collection
    {
        if ($this->pids->isEmpty()) {
            $this->pids = collect(explode(',', file_get_contents($this->file)));
        }
        return $this->pids;
    }

    protected function filter(int $pid): Collection
    {
        $pids = $this->all()->filter(function ($item) use ($pid) {
            return $pid === intval($item);
        });

        if ($pids->isEmpty()) {
            if (Process::kill(intval($pid), 0)) {
                return collect([$pid]);
            }
        }

        return $pids;
    }

}