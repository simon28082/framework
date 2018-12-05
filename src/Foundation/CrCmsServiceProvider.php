<?php

namespace CrCms\Framework\Foundation;

use CrCms\Foundation\Transporters\AbstractValidateDataProvider;
use CrCms\Foundation\Transporters\Contracts\DataProviderContract;
use CrCms\Foundation\Transporters\DataServiceProvider;
use CrCms\Framework\Console\Commands\ConfigCacheCommand;
use CrCms\Framework\Console\Commands\RouteCacheCommand;
use CrCms\Framework\Http\Commands\ServerCommand;
use CrCms\Server\AbstractServerCommand;
use Illuminate\Support\AggregateServiceProvider;

class CrCmsServiceProvider extends AggregateServiceProvider
{
    protected $providers = [
        DataServiceProvider::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->resolving(AbstractValidateDataProvider::class, function (AbstractValidateDataProvider $dataProvider, $app) {
            $dataProvider->setObject(
                array_merge($this->app['request']->route()->parameters() ?? [], $this->app['request']->all())
            );
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->registerAlias();

        $this->registerServices();

        $this->registerCommands();
    }

    /**
     * @return void
     */
    protected function registerServices(): void
    {
        $this->app->extend('data.provider', function (DataProviderContract $dataProvider) {
            return $dataProvider->setObject(
                array_merge($this->app['request']->route()->parameters() ?? [], $this->app['request']->all())
            );
        });

        $this->app->extend('command.route.cache', function () {
            return new RouteCacheCommand($this->app['files']);
        });

        $this->app->extend('command.config.cache', function () {
            return new ConfigCacheCommand($this->app['files']);
        });
    }

    /**
     * @return void
     */
    protected function registerCommands(): void
    {
        //require swoole-server
        if (class_exists(AbstractServerCommand::class)) {
            $this->commands(ServerCommand::class);
        }
    }

    /**
     * @return void
     */
    protected function registerAlias(): void
    {
    }
}
