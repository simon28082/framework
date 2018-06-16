<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/16 12:11
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\Server;


use function CrCms\Foundation\App\Helpers\framework_config_path;
use CrCms\Foundation\Swoole\Server\Contracts\StartActionContract;
use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use function CrCms\Foundation\App\Helpers\array_merge_recursive_distinct;
use Swoole\Process\Pool;

class ServerManage implements StartActionContract
{
//    protected $config;
//
//    public function __construct(Config $config)
//    {
//        $this->config = $config;
//    }

    protected $app;

    /**
     * @var array
     */
    protected $servers;

    protected $config;


    /**
     * @var Pool
     */
    protected $pool;

    public function __construct(Container $app)
    {
        $this->app = $app;



        $this->loadConfiguration();

//        $this->createServers();

        $this->createPool();
    }


    protected function createServers()
    {
        foreach ($this->config['servers'] as $server) {

            $drive = $this->config['drives'][$server['drive']] ?? '';

            if (!empty($drive) && class_exists($drive)) {
                $this->servers[] = new $drive($this->app, $server);
            }
        }
    }


    protected function createPool()
    {

//        for ($i = 0; $i < count($this->servers); $i++)
//        {
//            $p = new \swoole_process(function () use ($i) {
//                $this->servers[$i]->start();
//            }, false, false);
//            $p->start();
//        }

        $workerNum = 10;
        $this->pool = new \Swoole\Process\Pool(count($this->config['servers']));
$this->createServers();
        $this->pool->on("WorkerStart", function ($pool, $workerId) {
            $this->servers[$workerId]->start();
        });
//
//        $pool->on("WorkerStop", function ($pool, $workerId) {
//            echo "Worker#{$workerId} is stopped\n";
//        });
//
//        $pool->start();

//        $this->pool = new Pool(count($this->servers));
//        $this->pool->on('workerStart', [$this, 'onWorkStart']);
//        $this->pool->on('workerStop', [$this, 'onWorkerStop']);
        $this->pool->start();
    }


    protected function onWorkStart(Pool $pool, int $workdId): void
    {
        $this->servers[$workdId]->start();
    }


    protected function onWorkerStop(Pool $pool, $workdId): void
    {

    }

    protected function createHttpServer()
    {

    }

    protected function createWebSocketServer()
    {

    }

    protected function loadConfiguration(): void
    {
        $config = require framework_config_path('swoole.php');

        $customConfigPath = config_path('swoole.php');
        if (file_exists($customConfigPath) && is_file(file_exists($customConfigPath))) {
            $config = array_merge_recursive_distinct($config, require $customConfigPath);
        }

        $this->config = $config;
    }

    protected function mergeConfig()
    {
        $config = $this->config['servers'][$key];
        $config['settings'] = array_merge($this->config['public_settings'], $config['settings']);
        $config['events'] = array_merge($this->config['public_events'], $config['events']);
        return $config;
    }

    protected function p(array $servers)
    {
        $pool = new Pool(count($servers));

        $pool->on('workerStart', function ($pool, $workerId) use ($servers) {
            $servers[$workerId]->start();
        });
    }

//
//    protected function createRpcServer()
//    {
//
//    }

    public function start(): bool
    {
        return true;
//        $this->pool->start();
    }

    public function stop(): bool
    {
        return true;
        // TODO: Implement stop() method.
    }

    public function restart(): bool
    {
        return true;
        // TODO: Implement restart() method.
    }
}