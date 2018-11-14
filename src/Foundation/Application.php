<?php

namespace CrCms\Framework\Foundation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\PackageManifest as BasePackageManifest;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application as BaseApplication;
use Illuminate\Foundation\ProviderRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class Application
 * @package CrCms
 */
class Application extends BaseApplication implements Container
{

    /**
     * Get the path to the cached services.php file.
     *
     * @return string
     */
    public function getCachedServicesPath(): string
    {
        return $this->storagePath() . '/run-cache/' . getenv('CRCMS_MODE') . '/services.php';
    }

    /**
     * Get the path to the cached packages.php file.
     *
     * @return string
     */
    public function getCachedPackagesPath(): string
    {
        return $this->storagePath() . '/run-cache/' . getenv('CRCMS_MODE') . '/packages.php';
    }

    /**
     * Get the path to the configuration cache file.
     *
     * @return string
     */
    public function getCachedConfigPath(): string
    {
        return $this->storagePath() . '/run-cache/' . getenv('CRCMS_MODE') . '/config.php';
    }

    /**
     * @return string
     */
    public function getCachedRoutesPath(): string
    {
        return $this->storagePath() . '/run-cache/' . getenv('CRCMS_MODE') . '/routes.php';
    }

    /**
     * @param string $path
     * @return string
     */
//    public function bootstrapPath($path = ''): string
//    {
//        return $this->frameworkPath . DIRECTORY_SEPARATOR . 'src/Bootstrap/' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
//    }

    /**
     * @return void
     */
    public function registerCoreContainerAliases()
    {
        parent::registerCoreContainerAliases();
        $this->alias('app', static::class);
    }

    /**
     * @return void
     */
    public function registerConfiguredProviders()
    {
        $providers = Collection::make($this->config['mount.providers'])
            ->partition(function ($provider) {
                return Str::startsWith($provider, 'Illuminate\\');
            });

        $providers->splice(1, 0, [$this->make(BasePackageManifest::class)->providers()]);

        (new ProviderRepository($this, new Filesystem, $this->getCachedServicesPath()))
            ->load($providers->collapse()->toArray());
    }

    /**
     * Register the basic bindings into the container.
     *
     * @return void
     */
    protected function registerBaseBindings()
    {
        parent::registerBaseBindings();

        $this->instance(BasePackageManifest::class, new PackageManifest(
            new Filesystem, $this->basePath(), $this->getCachedPackagesPath()
        ));
    }
}