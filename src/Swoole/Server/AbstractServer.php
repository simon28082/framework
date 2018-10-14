<?php

namespace CrCms\Foundation\Swoole\Server;

use CrCms\Foundation\Swoole\Server\Contracts\ServerActionContract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Exception;
use Swoole\Process;
use Swoole\Server as SwooleServer;
use BadMethodCallException;

/**
 * Class AbstractServer
 * @package CrCms\Foundation\Swoole\Server
 */
abstract class AbstractServer implements ServerActionContract
{
    /**
     * @var SwooleServer
     */
    protected $server;

    /**
     * @var Container
     */
    protected $app;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $defaultEvents = [
        'start' => \CrCms\Foundation\Swoole\Server\Events\StartEvent::class,
        'worker_start' => \CrCms\Foundation\Swoole\Server\Events\WorkerStartEvent::class,
        'worker_stop' => '',
        'worker_exit' => '',
        'connect' => '',
        'receive' => '',
        'packet' => '',
        'close' => \CrCms\Foundation\Swoole\Server\Events\CloseEvent::class,
        'buffer_full' => '',
        'Buffer_empty' => '',
        'task' => '',
        'finish' => '',
        'pipe_message' => '',
        'worker_error' => '',
        'manager_start' => \CrCms\Foundation\Swoole\Server\Events\ManagerStartEvent::class,
        'manager_stop' => '',
    ];

    /**
     * @var array
     */
    protected $defaultSettings = [
        'package_max_length' => 1024 * 1024 * 10,
        'user' => 'daemon',
        'group' => 'daemon',
    ];

    /**
     * @var array
     */
    protected $events = [];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var Process
     */
    protected $process;

    /**
     * AbstractServer constructor.
     * @param Container $app
     * @param array $config
     */
    public function __construct(Container $app, array $config)//, \Swoole\Server $server = null)
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * @return void
     */
    abstract public function bootstrap(): void;

    /**
     * @param Process $process
     */
    public function setProcess(Process $process)
    {
        $this->process = $process;
    }

    /**
     * @return Process
     */
    public function getProcess(): Process
    {
        return $this->process;
    }

    /**
     * @return bool
     */
    public function start(): bool
    {
        return $this->server->start();
    }

    /**
     * @return bool
     */
    public function stop(): bool
    {
        return $this->server->shutdown();
    }

    /**
     * @return bool
     */
    public function restart(): bool
    {
        return $this->server->reload();
    }

    /**
     * @return SwooleServer
     */
    public function getServer(): SwooleServer
    {
        return $this->server;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return Container
     */
    public function getApp(): Container
    {
        return $this->app;
    }

    /**
     * @param array $settings
     * @return void
     */
    protected function setSettings(array $settings): void
    {
        $this->server->set(array_merge($this->defaultSettings, $this->settings, $settings));
    }

    /**
     * @return void
     */
    protected function eventDispatcher(array $events): void
    {
        collect(array_merge($this->defaultEvents, $this->events, $events))->filter(function (string $event) {
            return class_exists($event);
        })->each(function (string $event, string $name) {
            $this->eventsCallback(Str::camel($name), $event);
        });
    }

    /**
     * @param string $name
     * @param string $event
     * @return void
     */
    protected function eventsCallback(string $name, string $event): void
    {
        $this->server->on($name, function () use ($name, $event) {
            $this->setServerEventAttribute($name, $event, $this->filterServer(func_get_args()));
            $this->eventHandle($name);
        });
    }

    /**
     * @param array $args
     * @return array
     */
    protected function filterServer(array $args): array
    {
        return collect($args)->filter(function ($item) {
            return !($item instanceof \Swoole\Server);
        })->toArray();
    }

    /**
     * @param string $name
     * @param string $event
     * @param array $args
     * @return void
     */
    protected function setServerEventAttribute(string $name, string $event, array $args): void
    {
        $this->server->{$name} = new $event(...$args);
    }

    /**
     * @param string $name
     * @return void
     */
    protected function eventHandle(string $name): void
    {
        $this->server->{$name}->handle($this);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if (isset($this->server->{$name})) {
            return $this->server->{$name};
        }

        throw new InvalidArgumentException('The attributes is not exists');
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->server, $name)) {
            return $this->server->{$name}(...$arguments);
        }

        throw new BadMethodCallException("The method:[{$name}] not exists");
    }
}