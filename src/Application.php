<?php

namespace CrCms\Foundation;

use CrCms\Foundation\Foundation\PackageManifest;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\PackageManifest as BasePackageManifest;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application as BaseApplication;
use CrCms\Foundation\ServerApplication as ServerApplicationContract;
use CrCms\Foundation\Laravel\Application as LaravelApplication;
use BadMethodCallException;

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
     * @var bool
     */
    protected $hasBeenDeferredBootstrapped = false;

    /**
     * @var ServerApplicationContract
     */
    protected $serverApplication;

    /**
     * Application constructor.
     * @param null|string $basePath
     * @param ServerApplication|null $application
     */
    public function __construct(?string $basePath = null, ?ServerApplicationContract $application = null)
    {
        $this->initServiceApplication($application);
        parent::__construct($basePath);
    }

    /**
     * @param ServerApplication|null $application
     */
    public function initServiceApplication(?ServerApplicationContract $application = null)
    {
        $this->serverApplication = $application ? $application : $this->defaultServerApplication();
        $this->serverApplication->setApp($this);
        $this->instance('app.server', $this->serverApplication);
        $this->instance(ServerApplicationContract::class, $this->serverApplication);
    }

    /**
     * @param array $bootstrappers
     */
    public function deferredBootstrapWith(array $bootstrappers)
    {
        $this->hasBeenDeferredBootstrapped = true;

        foreach ($bootstrappers as $bootstrapper) {
            $this['events']->fire('bootstrapping: ' . $bootstrapper, [$this]);

            $this->make($bootstrapper)->bootstrap($this);

            $this['events']->fire('bootstrapped: ' . $bootstrapper, [$this]);
        }
    }

    /**
     * Determine if the application has been bootstrapped before.
     *
     * @return bool
     */
    public function hasBeenDeferredBootstrapped()
    {
        return $this->hasBeenDeferredBootstrapped;
    }

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
     * @return array
     */
    public function getServiceProviders(): array
    {
        return $this->serviceProviders;
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
    public function getCachedServicesPath(): string
    {
        return $this->serverApplication->getCachedServicesPath();
    }

    /**
     * Get the path to the cached packages.php file.
     *
     * @return string
     */
    public function getCachedPackagesPath(): string
    {
        return $this->serverApplication->getCachedPackagesPath();
    }

    /**
     * Get the path to the configuration cache file.
     *
     * @return string
     */
    public function getCachedConfigPath(): string
    {
        return $this->serverApplication->getCachedConfigPath();
    }

    /**
     * @return string
     */
    public function getCachedRoutesPath(): string
    {
        return $this->serverApplication->getCachedRoutesPath();
    }

    /**
     * @param string $path
     * @return string
     */
    public function bootstrapPath($path = ''): string
    {
        return $this->frameworkPath . DIRECTORY_SEPARATOR . 'src' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * @return void
     */
    public function registerCoreContainerAliases()
    {
        parent::registerCoreContainerAliases();
        $this->alias('app', self::class);
        $this->alias('app.server', get_class($this->serverApplication));
        $this->alias('app.server', ServerApplicationContract::class);
    }

    /**
     * @return ServerApplication
     */
    public function defaultServerApplication(): ServerApplication
    {
        return new LaravelApplication;
    }

    /**
     * @return ServerApplication
     */
    public function getServerApplication(): ServerApplicationContract
    {
        return $this->serverApplication;
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
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->serverApplication, $name)) {
            return $this->serverApplication->$name(...$arguments);
        }

        throw new BadMethodCallException("The method[{$name}] is not exists");
    }
}