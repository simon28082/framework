<?php

namespace CrCms\Foundation;

use CrCms\Foundation\Foundation\PackageManifest;
use CrCms\Foundation\Routing\Router;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\PackageManifest as BasePackageManifest;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application as BaseApplication;
use Illuminate\Log\LogServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Events\EventServiceProvider;
use CrCms\Foundation\Routing\RoutingServiceProvider;

/**
 * Class Application
 * @package CrCms
 */
class Application extends BaseApplication implements Container
{
    /**
     * @var string
     */
    protected $modulePath;

    /**
     * @var string
     */
    protected $extensionPath;

    /**
     * @var string
     */
    protected $frameworkPath;

    /**
     * @var string
     */
    protected $frameworkConfigPath;

    /**
     * @var string
     */
    protected $frameworkResourcePath;

    /**
     * @return void
     */
    protected function bindPathsInContainer()
    {
        $this->useFrameworkPath($this->frameworkPath());
        $this->useFrameworkConfigPath($this->frameworkConfigPath());
        $this->useFrameworkResourcePath($this->frameworkResourcePath());
        $this->useModulePath($this->modulePath());
        $this->useExtensionPath($this->extensionPath());

        parent::bindPathsInContainer();
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

    /**
     * Register all of the base service providers.
     *
     * @return void
     */
    protected function registerBaseServiceProviders()
    {
        $this->register(new EventServiceProvider($this));

        $this->register(new LogServiceProvider($this));

        $this->register(new RoutingServiceProvider($this));
    }

    /**
     * @return string
     */
    public function frameworkPath(string $path = ''): string
    {
        return realpath(__DIR__ . '/../') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * @param string $path
     * @return $this
     */
    public function useFrameworkPath(string $path)
    {
        $this->frameworkPath = $path;
        $this->instance('path.framework', $path);

        return $this;
    }

    /**
     * @return string
     */
    public function frameworkResourcePath(string $path = ''): string
    {
        return $this->frameworkPath('resources') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * @param string $path
     * @return $this
     */
    public function useFrameworkResourcePath(string $path)
    {
        $this->frameworkResourcePath = $path;
        $this->instance('path.framework_resource', $path);

        return $this;
    }

    /**
     * @param string $path
     * @return string
     */
    public function frameworkConfigPath(string $path = ''): string
    {
        return $this->frameworkPath('config') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * @param string $path
     * @return $this
     */
    public function useFrameworkConfigPath(string $path)
    {
        $this->frameworkConfigPath = $path;
        $this->instance('path.framework_config', $path);

        return $this;
    }

    /**
     * @return string
     */
    public function modulePath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'modules' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * @param string $path
     * @return $this
     */
    public function useModulePath(string $path)
    {
        $this->modulePath = $path;
        $this->instance('path.module', $path);

        return $this;
    }

    /**
     * @return string
     */
    public function extensionPath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'extensions' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * @param string $path
     * @return $this
     */
    public function useExtensionPath(string $path)
    {
        $this->extensionPath = $path;
        $this->instance('path.extension', $path);

        return $this;
    }

    /**
     * Get the path to the cached services.php file.
     *
     * @return string
     */
    public function getCachedServicesPath()
    {
        return $this->storagePath() . '/run-cache/services.php';
    }

    /**
     * Get the path to the cached packages.php file.
     *
     * @return string
     */
    public function getCachedPackagesPath()
    {
        return $this->storagePath() . '/run-cache/packages.php';
    }

    /**
     * Get the path to the configuration cache file.
     *
     * @return string
     */
    public function getCachedConfigPath()
    {
        return $this->storagePath() . '/run-cache/config.php';
    }

    /**
     * @return string
     */
    public function getCachedRoutesPath()
    {
        return $this->storagePath() . '/run-cache/routes.php';
    }

    /**
     * @param string $path
     * @return string
     */
    public function bootstrapPath($path = '')
    {
        return $this->frameworkPath . DIRECTORY_SEPARATOR . 'src/Bootstrap' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     *
     */
    public function registerCoreContainerAliases()
    {
        parent::registerCoreContainerAliases();
        $this->alias('app', self::class);
        $this->alias('router',Router::class);
    }
}