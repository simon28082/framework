<?php

namespace CrCms\Foundation;

use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application as BaseApplication;

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
     * @return void
     */
    protected function bindPathsInContainer()
    {
        parent::bindPathsInContainer();

        $this->useModulePath($this->modulePath());
        $this->useExtensionPath($this->extensionPath());
    }

    /**
     * @return string
     */
    public function modulePath(): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'modules';
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
    public function extensionPath(): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'extensions';
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
     * 
     */
    public function registerCoreContainerAliases()
    {
        parent::registerCoreContainerAliases();
        $this->alias('app',self::class);
    }
}