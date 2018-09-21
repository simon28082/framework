<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/07/02 19:15
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc;

use CrCms\Foundation\Rpc\Client\Drivers\Http;
use CrCms\Foundation\Rpc\Contracts\Selector;
use CrCms\Foundation\Rpc\Client\Selectors\RandSelector;
use CrCms\Foundation\Rpc\Client\ServiceDiscover;
use CrCms\Foundation\Rpc\Commands\ServiceRegisterCommand;
use CrCms\Foundation\Rpc\Contracts\RpcContract;
use CrCms\Foundation\Rpc\Contracts\ServiceDiscoverContract;
use Illuminate\Support\ServiceProvider;

/**
 * Class RpcServiceProvider
 * @package CrCms\Foundation\Rpc
 */
class RpcServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * @return void
     */
    public function register(): void
    {
        $this->registerAlias();
        $this->registerCommands();
        $this->registerServices();
    }

    /**
     * @return void
     */
    protected function registerServices(): void
    {
        // @todo 应该有一个封装好的工厂方法，先这样吧
        $this->app->bind(RpcContract::class, function ($app) {
            $driver = $app->make('config')->get('rpc.connections.consul.driver');
            switch ($driver['name']) {
                case 'http':
                    return new Http($app->make('client.manager'),$driver);
            }
        });

        $this->app->singleton('rpc.discovery.selector', function () {
            return new RandSelector;
        });

        $this->app->singleton('rpc.discovery', function ($app) {
            return new ServiceDiscover($app, $app->make('rpc.discovery.selector'), $app->make('client.manager'));
        });
    }

    /**
     * @return void
     */
    protected function registerCommands(): void
    {
        $this->commands(ServiceRegisterCommand::class);
    }

    /**
     * @return void
     */
    protected function registerAlias(): void
    {
        $this->app->alias('rpc.discovery', ServiceDiscoverContract::class);
        $this->app->alias('rpc.discovery.selector', Selector::class);
    }

    /**
     * @return array
     */
    public function provides(): array
    {
        return [
            RpcContract::class,
            ServiceDiscoverContract::class,
            Selector::class,
            ServiceRegisterCommand::class,
        ];
    }
}