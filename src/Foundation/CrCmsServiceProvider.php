<?php

namespace CrCms\Framework\Foundation;

use CrCms\Foundation\Transporters\Contracts\DataProviderContract;
use CrCms\Foundation\Transporters\DataProvider;
use CrCms\Framework\Console\Commands\ConfigCacheCommand;
use CrCms\Framework\Console\Commands\RouteCacheCommand;
use Illuminate\Support\ServiceProvider;

class CrCmsServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAlias();

        $this->registerServices();

        $this->registerCommands();
    }

    /**
     * @return void
     */
    protected function registerServices(): void
    {
        $this->app->bind('data.provider', function ($app) {
            return new DataProvider($app['request']);
        });

//        $this->app->singleton('command.crcms.make.directory', function ($app) {
//            return new DirectoryMakeCommand($app['files']);
//        });

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
        //$this->commands('command.crcms.make.directory');
    }

    /**
     * @return void
     */
    protected function registerAlias(): void
    {
        $this->app->alias('data.provider', DataProviderContract::class);
//        $this->app->alias('command.crcms.make.directory', DirectoryMakeCommand::class);
        $this->app->alias('command.route.cache', RouteCacheCommand::class);
    }

    /**
     * @return array
     */
    public function provides(): array
    {
        return [
            'data.provider',
//            'command.crcms.make.directory',
        ];
    }
}
