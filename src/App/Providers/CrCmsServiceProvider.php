<?php

namespace CrCms\Foundation\App\Providers;

use CrCms\Foundation\Console\Commands\DirectoryMakeCommand;
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
    }

    /**
     *
     */
    protected function registerDirectoryMakeCommand()
    {
        $this->app->singleton('command.crcms.make.directory', function ($app) {
            return new DirectoryMakeCommand($app['files']);
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
