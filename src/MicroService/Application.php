<?php

namespace CrCms\Foundation\MicroService;

use Illuminate\Foundation\PackageManifest;
use CrCms\Foundation\ServerApplication as ServerApplicationContract;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\ProviderRepository;
use CrCms\Foundation\Application as BaseApplication;
use Illuminate\Support\Str;

/**
 * Class Application
 * @package CrCms\Foundation\MicroService
 */
class Application implements ServerApplicationContract
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
        return 'Micro-Service';
    }

    /**
     * @return void
     */
    public function loadKernel(): void
    {
        $this->app->singleton(
            \CrCms\Foundation\MicroService\Contracts\Kernel::class,
            \CrCms\Foundation\MicroService\Kernel::class
        );
    }

    /**
     * @return void
     */
    public function registerConfiguredProviders(): void
    {
        $serverProviders = Collection::make($this->app->config['micro-service.providers']);
        $disableProviders = Collection::make($this->app->config['micro-service.disable_providers']);

        $providers = $this->app->getRegisterConfiguredProviders()->merge($serverProviders)->unique()->diff($disableProviders)->values();

        (new ProviderRepository($this->app, new Filesystem, $this->app->getCachedServicesPath()))
            ->load($providers->toArray());
    }

    /**
     * Get the path to the cached services.php file.
     *
     * @return string
     */
    public function getCachedServicesPath(): string
    {
        return $this->app->storagePath() . '/run-cache/micro-service/services.php';
    }

    /**
     * @return string
     */
    public function getCachedPackagesPath(): string
    {
        return $this->app->storagePath() . '/run-cache/micro-service/packages.php';
    }

    /**
     * Get the path to the configuration cache file.
     *
     * @return string
     */
    public function getCachedConfigPath(): string
    {
        return $this->app->storagePath() . '/run-cache/micro-service/config.php';
    }

    /**
     * @return string
     */
    public function getCachedRoutesPath(): string
    {
        return $this->app->storagePath() . '/run-cache/micro-service/routes.php';
    }
}