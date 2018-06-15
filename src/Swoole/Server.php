<?php

namespace CrCms\Foundation\Swoole;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Exception;
use Swoole\Server as SwooleServer;

class Server
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

    const SERVER_TYPE_MASTER = 1;

    const SERVER_TYPE_SALVE = 2;

    /**
     * Server constructor.
     * @param Container $app
     */
    public function __construct(Container $app, array $config, \Swoole\Server $server = null)
    {
        $this->app = $app;
        $this->config = $config;

        if ($server) {
            $this->createSalve($server);
        } else {
            $this->createMaster();
        }

//        $this->initialization();
        $this->resolveEvents();
    }

    /**
     *
     */
//    protected function setConfig()
//    {
//        $this->config = require $this->app->configPath('swoole.php');
//    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }


    protected function createMaster()
    {
        $serverDrive = $this->config['drive'];
        $serverParams = [
            $this->config['host'],
            $this->config['port'],
            $this->config['mode'],
            $this->config['type'],
        ];//array_merge([$this->config['host'], $this->config['port']], $this->config['servers'][$this->config['server_type']]['params']);
        $this->server = new $serverDrive(...$serverParams);
        $this->server->set($this->config['settings']);
    }

    protected function createSalve(\Swoole\Server $master)
    {
        if ($master instanceof \Swoole\Http\Server || $master instanceof \Swoole\WebSocket\Server) {
            $this->server = $master->addListener($this->config['host'],$this->config['port'],$this->config['type']);
        } else {
            $this->server = $master->listen($this->config['host'],$this->config['port'],$this->config['type']);
        }
        $this->server->set($this->config['settings']);
    }

    /**
     * @return void
     */
//    protected function initialization(): void
//    {
//        $serverDrive = $this->config['drive'];
//        $serverParams = [
//            $this->config['host'],
//            $this->config['port'],
//            $this->config['mode'],
//            $this->config['type'],
//        ];//array_merge([$this->config['host'], $this->config['port']], $this->config['servers'][$this->config['server_type']]['params']);
//        $this->server = new $serverDrive(...$serverParams);
//        $this->server->set($this->config['settings']);
//    }


    /**
     * @return void
     */
    protected function resolveEvents(): void
    {
        foreach ($this->config['events'] as $name => $event) {
            if (is_array($event)) {
                if ($name === $this->config['server_type']) {
                    foreach ($event as $subName => $subEvent) {
                        $this->eventsCallback($subName, $subEvent);
                    }
                }
            } else {
                $this->eventsCallback($name, $event);
            }
        }
    }

    /**
     * @param string $name
     * @param string $event
     * @return void
     */
    protected function eventsCallback(string $name, string $event): void
    {
        if (!class_exists($event)) {
            return;
        }

        $this->server->on(Str::camel($name), function () use ($name, $event) {
            $this->setEvents(Str::camel($name), $event, $this->filterServerParams(func_get_args()));
            try {
                $this->server->{Str::camel($name)}->handle($this);
            } catch (Exception $exception) {
                throw $exception;
            }
        });
    }

    /**
     * @param array $args
     * @return array
     */
    protected function filterServerParams(array $args): array
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
    protected function setEvents(string $name, string $event, array $args): void
    {
        $this->server->{$name} = new $event(...$args);
    }

    /**
     * @return SwooleServer
     */
    public function getSwooleServer(): SwooleServer
    {
        return $this->server;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if (in_array(
            Str::snake($name),
            array_keys($this->config['events']), true
        )) {
            return $this->{Str::snake($name)};
        }

        throw new InvalidArgumentException('The attributes is not exists');
    }
}