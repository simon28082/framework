<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/28 20:42
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc;

use CrCms\Foundation\Rpc\Client\ConnectionFactory;
use CrCms\Foundation\Rpc\Client\ConnectionManager;
use CrCms\Foundation\Rpc\Client\ConnectionPool;
use CrCms\Foundation\Rpc\Client\Contracts\ConnectionPool as ConnectionPoolContract;
use CrCms\Foundation\Rpc\Client\Contracts\Selector;
use CrCms\Foundation\Rpc\Client\Selectors\RandSelector;
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
     *
     */
    public function register()
    {
        $this->registerAlias();

        $this->registerConnectionServices();
    }

    /**
     *
     */
    protected function registerConnectionServices()
    {
        $this->app->singleton(ConnectionFactory::class, function ($app) {
            return new ConnectionFactory($app);
        });

        $this->app->singleton(Selector::class, $app['config']->get('rpc.selector', RandSelector::class));

        $this->app->singleton(ConnectionPoolContract::class, function ($app) {
            return new ConnectionPool($app[Selector::class]);
        });

        $this->app->singleton(ConnectionManager::class, function ($app) {
            return new ConnectionManager($app, $app->make('rpc.client.factory'), $app->make('rpc.client.pool'));
        });
    }

    /**
     *
     */
    protected function registerAlias()
    {
        $this->app->alias('rpc.client.factory', ConnectionFactory::class);
        $this->app->alias('rpc.client.pool', ConnectionPoolContract::class);
        $this->app->alias('rpc.client.manager', ConnectionManager::class);
    }

    /**
     * @return array
     */
    public function provides(): array
    {
        return [
            Selector::class,
            'rpc.client.factory',
            'rpc.client.pool',
            'rpc.client.manager'
        ];
    }
}