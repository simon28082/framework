<?php

namespace CrCms\Foundation\Http;

use CrCms\Foundation\Foundation\Contracts\ApplicationContract;
use CrCms\Foundation\Foundation\PackageManifest;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\PackageManifest as BasePackageManifest;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application as BaseApplication;
use CrCms\Foundation\ServerApplication as ServerApplicationContract;
use CrCms\Foundation\Laravel\Application as LaravelApplication;
use BadMethodCallException;
use Illuminate\Foundation\ProviderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class Application
 * @package CrCms
 */
class Application extends BaseApplication implements Container, ApplicationContract
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
     * @return void
     */
    public function registerConfiguredProviders(): void
    {
        $providers = Collection::make($this->config['app.providers'])
            ->merge(Collection::make($this->config['http.providers']))
            ->partition(function ($provider) {
                return Str::startsWith($provider, 'Illuminate\\');
            });

        $providers->splice(2, 0, [$this->make(BasePackageManifest::class)->providers()]);

        $disableProviders = Collection::make($this->config['http.disable_providers']);

        $providers = $providers->collapse()->unique()->diff($disableProviders)->values();

        (new ProviderRepository($this, new Filesystem, $this->getCachedServicesPath()))
            ->load($providers->toArray());
    }

    /**
     * @return void
     */
    public function bindKernel(): void
    {
        $this->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \CrCms\Foundation\App\Exceptions\Handler::class
        );

        $this->singleton(
            \Illuminate\Contracts\Http\Kernel::class,
            \CrCms\Foundation\Http\Kernel::class
        );
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $kernel = $this->make(\Illuminate\Contracts\Http\Kernel::class);

        $response = $kernel->handle(
            $request = Request::capture()
        );

        $response->send();

        $kernel->terminate($request, $response);
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
        return $this->storagePath() . '/run-cache/http/services.php';
    }

    /**
     * Get the path to the cached packages.php file.
     *
     * @return string
     */
    public function getCachedPackagesPath(): string
    {
        return $this->storagePath() . '/run-cache/http/packages.php';
    }

    /**
     * Get the path to the configuration cache file.
     *
     * @return string
     */
    public function getCachedConfigPath(): string
    {
        return $this->storagePath() . '/run-cache/http/config.php';
    }

    /**
     * @return string
     */
    public function getCachedRoutesPath(): string
    {
        return $this->storagePath() . '/run-cache/http/routes.php';
    }

    /**
     * @param string $path
     * @return string
     */
    public function bootstrapPath($path = ''): string
    {
        return $this->frameworkPath . DIRECTORY_SEPARATOR . 'src/Bootstrap/' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * @return void
     */
    public function registerCoreContainerAliases()
    {
        parent::registerCoreContainerAliases();
        $this->alias('app', self::class);
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