<?php

namespace CrCms\Foundation\Swoole;

use BadFunctionCallException;

/**
 * Class INotify
 * @package CrCms\Foundation\Start\Drivers\Swoole
 */
class INotify
{
    /**
     * @var array
     */
    protected $targets;

    /**
     * @var resource
     */
    protected $resource;

    /**
     * INotify constructor.
     * @param array $targets
     */
    public function __construct(array $targets)
    {
        $this->targets = $targets;
        $this->setResource();
        $this->watch();
    }

    /**
     * @return void
     */
    protected function setResource(): void
    {
        if (!function_exists('inotify_init')) {
            throw new BadFunctionCallException('The function inotify_init is not exists');
        }

        $this->resource = inotify_init();
    }

    /**
     * @return void
     */
    protected function watch(): void
    {
        foreach ($this->targets as $target) {
            if (file_exists($target)) {
                inotify_add_watch($this->resource, $target, IN_ALL_EVENTS);//IN_CREATE | IN_MODIFY | IN_DELETE
            }
        }
    }

    /**
     * @param callable $callback
     * @return void
     */
    public function monitor(callable $callback): void
    {
        swoole_event_add($this->resource, function () use ($callback) {
            $events = inotify_read($this->resource);
            call_user_func_array($callback, [$events]);
        });

        swoole_event_wait();
    }
}