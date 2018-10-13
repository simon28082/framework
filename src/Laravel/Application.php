<?php

namespace CrCms\Foundation\Laravel;

use CrCms\Foundation\ServerApplication;
use CrCms\Foundation\Application as BaseApplication;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\ProviderRepository;

/**
 * Class Application
 * @package CrCms\Foundation\Laravel
 */
class Application implements ServerApplication
{
    protected $app;
//
//    public function __construct(BaseApplication $application)
//    {
//        $this->app = $application;
//    }

    public function setApp(BaseApplication $app)
    {
        $this->app = $app;
    }

    public function loadKernel(): void
    {
        $this->app->singleton(
            \Illuminate\Contracts\Http\Kernel::class,
            \CrCms\Foundation\Http\Kernel::class
        );
    }

    public function registerConfiguredProviders(): void
    {
        $providers = Collection::make($this->app->make('config')->get('http.providers'));

        (new ProviderRepository($this->app, new Filesystem, $this->app->getCachedServicesPath()))
            ->load($providers->toArray());
    }

    public function getCachedServicesPath(): string
    {
        return $this->app->storagePath() . '/run-cache/laravel/services.php';
    }

    public function getCachedPackagesPath(): string
    {
        return $this->app->storagePath() . '/run-cache/laravel/packages.php';
    }

    public function getCachedConfigPath(): string
    {
        return $this->app->storagePath() . '/run-cache/laravel/config.php';
    }

    public function getCachedRoutesPath(): string
    {
        return $this->app->storagePath() . '/run-cache/laravel/routes.php';
    }
}