<?php

namespace CrCms\Foundation\Swoole;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;

class ServerManage
{
    /**
     * @var Server
     */
    protected $master;

    /**
     * @var array
     */
    protected $salves;
    
    protected $app;

    protected $config;



    public function __construct(Application $app, array $config)
    {
        $this->app = $app;
        $this->config = $config;
        $this->createServer();
    }

    protected function createMasterServer()
    {
        $this->master = new Server($this->app,$this->mergeConfig($this->config['active_servers']['master']));
    }

    protected function createServer()
    {
        $this->createMasterServer();
        $this->createSalveServer();
    }

    protected function createSalveServer()
    {
        foreach ($this->config['active_servers']['salve'] as $value) {
            $this->salves[$value] = new Server(
                $this->app,
                $this->mergeConfig($value),
                $this->master->getSwooleServer()
            );
        }
    }


    /**
     * @param Container $app
     */
    public function start(): void
    {
        $this->master->getSwooleServer()->start();
    }

    public function reload()
    {
        $this->master->getSwooleServer()->reload();
    }

    protected function mergeConfig($key): array
    {
        $config = $this->config['servers'][$key];
        $config['settings'] = array_merge($this->config['public_settings'],$config['settings']);
        $config['events'] = array_merge($this->config['public_events'],$config['events']);
        return $config;
    }

    public function getMaster(): Server
    {
        return $this->master;
    }

    public function getSalves(): array
    {
        return $this->salves;
    }
}