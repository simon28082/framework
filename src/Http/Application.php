<?php

namespace CrCms\Foundation\Http;

use CrCms\Foundation\Swoole\Server\Contracts\ServerBindApplicationContract;
use CrCms\Foundation\Swoole\Server\Contracts\ServerContract;
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
class Application implements ServerApplicationContract, ServerBindApplicationContract
{
    /**
     * @var BaseApplication
     */
    protected $app;

    /**
     * @var ServerContract
     */
    protected $server;

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
        return 'http';
    }

    /**
     * @param ServerContract $server
     * @return void
     */
    public function bindServer(ServerContract $server): void
    {
        $this->server = $server;
    }

    /**
     * @return ServerContract
     */
    public function getServer(): ServerContract
    {
        return $this->server;
    }

    /**
     * @return void
     */
    public function loadKernel(): void
    {
        $this->app->singleton(
            \Illuminate\Contracts\Http\Kernel::class,
            \CrCms\Foundation\Http\Kernel::class
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
        $providers = $this->app->config['http.reload_providers'];

        foreach ($providers as $provider) {
            $this->app->register($provider,true);
            $provider = $this->app->getProvider($provider);
            if (method_exists($provider, 'boot')) {
                $provider->boot();
            }
        }
    }


    /**
     * @return void
     */
    public function registerConfiguredProviders(): void
    {
        $serverProviders = Collection::make($this->app->config['http.providers']);
        $disableProviders = Collection::make($this->app->config['http.disable_providers']);

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
        return $this->app->storagePath() . '/run-cache/http/services.php';
    }

    /**
     * @return string
     */
    public function getCachedPackagesPath(): string
    {
        return $this->app->storagePath() . '/run-cache/http/packages.php';
    }

    /**
     * Get the path to the configuration cache file.
     *
     * @return string
     */
    public function getCachedConfigPath(): string
    {
        return $this->app->storagePath() . '/run-cache/http/config.php';
    }

    /**
     * @return string
     */
    public function getCachedRoutesPath(): string
    {
        return $this->app->storagePath() . '/run-cache/http/routes.php';
    }
}