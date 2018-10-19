<?php

namespace CrCms\Foundation\Providers;

use CrCms\Foundation\Console\Commands\DirectoryMakeCommand;
use CrCms\Foundation\Console\Commands\RouteCacheCommand;
use CrCms\Foundation\Transporters\Contracts\DataProviderContract;
use CrCms\Foundation\Transporters\DataProvider;
use Illuminate\Support\ServiceProvider;

class CrCmsServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

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
        $this->registerCommands();

        $this->app->bind(DataProviderContract::class, function ($app) {
            return new DataProvider($app['request']);
        });
    }

    /**
     *
     */
    protected function registerCommands()
    {
        $this->registerDirectoryMakeCommand();
        $this->commands('command.crcms.make.directory');
        $this->commands('command.route.cache');
    }

    /**
     *
     */
    protected function registerDirectoryMakeCommand()
    {
        $this->app->singleton('command.crcms.make.directory', function ($app) {
            return new DirectoryMakeCommand($app['files']);
        });

        $this->app->singleton('command.route.cache', function ($app) {
            return new RouteCacheCommand($app['files']);
        });
    }

    /**
     * @return array
     */
    public function provides(): array
    {
        return [
            DataProviderContract::class,
            'command.crcms.make.directory',
        ];
    }
}
