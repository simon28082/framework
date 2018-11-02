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
    /**
     * @var BaseApplication
     */
    protected $app;

    /**
     * @param BaseApplication $app
     * @return void
     */
    public function setApp(BaseApplication $app)
    {
        $this->app = $app;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Laravel';
    }

    /**
     * @return void
     */
    public function loadKernel(): void
    {
        $this->app->singleton(
            \Illuminate\Contracts\Http\Kernel::class,
            \CrCms\Foundation\Laravel\Kernel::class
        );
    }

    /**
     * @return string
     */
    public function kernel(): string
    {
        return \Illuminate\Contracts\Http\Kernel::class;
    }

    /**
     * @return void
     */
    public function reloadProviders(): void
    {
        return ;
    }

    /**
     * @return void
     */
    public function registerConfiguredProviders(): void
    {
        $serverProviders = Collection::make($this->app->config['http.providers']);
        $disableProviders = Collection::make($this->app->config['http.disable_providers'] ?? []);

        $providers = $this->app->getRegisterConfiguredProviders()->merge($serverProviders)->unique()->diff($disableProviders)->values();

        (new ProviderRepository($this->app, new Filesystem, $this->app->getCachedServicesPath()))
            ->load($providers->toArray());
    }

    /**
     * @return string
     */
    public function getCachedServicesPath(): string
    {
        return $this->app->storagePath() . '/run-cache/laravel/services.php';
    }

    /**
     * @return string
     */
    public function getCachedPackagesPath(): string
    {
        return $this->app->storagePath() . '/run-cache/laravel/packages.php';
    }

    /**
     * @return string
     */
    public function getCachedConfigPath(): string
    {
        return $this->app->storagePath() . '/run-cache/laravel/config.php';
    }

    /**
     * @return string
     */
    public function getCachedRoutesPath(): string
    {
        return $this->app->storagePath() . '/run-cache/laravel/routes.php';
    }
}