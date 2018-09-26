<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/07/02 19:15
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\MicroService;

use CrCms\Foundation\MicroService\Client\Contracts\Selector;
use CrCms\Foundation\MicroService\Client\Drivers\Restful;
use CrCms\Foundation\MicroService\Client\Selectors\RandSelector;
use CrCms\Foundation\MicroService\Client\ServiceDiscover;
use CrCms\Foundation\MicroService\Server\Commands\ServiceRegisterCommand;
use CrCms\Foundation\MicroService\Client\Contracts\RpcContract;
use CrCms\Foundation\MicroService\Client\Contracts\ServiceDiscoverContract;
use Illuminate\Support\ServiceProvider;

/**
 * Class MicroServiceProvider
 * @package CrCms\Foundation\Rpc
 */
class MicroServiceProvider extends ServiceProvider
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
        $this->registerServices();
//        if (getenv("CRCMS_RUN_MODE") === 'ARTISAN') {
            $this->registerCommands();
//        }
    }

    /**
     * @return void
     */
    protected function registerServices(): void
    {
        // @todo 应该有一个封装好的工厂方法，先这样吧
        $this->app->bind(RpcContract::class, function ($app) {
            $driver = $app->make('config')->get('micro-service.connections.consul.driver');
            switch ($driver['name']) {
                case 'restful':
                    return new Restful($app->make('client.manager'), $driver);
            }
        });

        $this->app->singleton('micro-service.discovery.selector', function () {
            return new RandSelector;
        });

        $this->app->singleton('micro-service.discovery', function ($app) {
            return new ServiceDiscover($app, $app->make('micro-service.discovery.selector'), $app->make('client.manager'));
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
        $this->app->alias('micro-service.discovery', ServiceDiscoverContract::class);
        $this->app->alias('micro-service.discovery.selector', Selector::class);
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