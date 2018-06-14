<?php

namespace CrCms\Foundation\Bootstrap;

use function CrCms\Foundation\App\Helpers\array_merge_recursive_adv;
use function CrCms\Foundation\App\Helpers\array_merge_recursive_distinct;
use Exception;
use SplFileInfo;
use Illuminate\Config\Repository;
use Symfony\Component\Finder\Finder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Foundation\Bootstrap\LoadConfiguration as BaseLoadConfiguration;

class LoadConfiguration extends BaseLoadConfiguration
{
    /**
     * Load the configuration items from all of the files.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Contracts\Config\Repository $repository
     * @return void
     * @throws \Exception
     */
    protected function loadConfigurationFiles(Application $app, RepositoryContract $repository)
    {
        $files = $this->getConfiguration($app);

        $frameworkFiles = $this->getFrameworkConfiguration($app);

        if (!isset($files['app']) && !isset($frameworkFiles['app'])) {
            throw new Exception('Unable to load the "app" configuration file.');
        }

        $mergeConfig = array_merge_recursive_distinct($frameworkFiles, $files);
        foreach ($mergeConfig as $key => $items) {
            $repository->set($key, $items);
        }
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return array
     */
    protected function getFrameworkConfiguration(\CrCms\Foundation\Application $app)
    {
        $files = [];

        $configPath = realpath($app->frameworkConfigPath());

        foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file) {
            $directory = $this->getNestedDirectory($file, $configPath);

            $files[$directory . basename($file->getRealPath(), '.php')] = require $file->getRealPath();
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return array
     */
    protected function getConfiguration(Application $app)
    {
        $files = [];

        $configPath = realpath($app->configPath());

        foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file) {
            $directory = $this->getNestedDirectory($file, $configPath);

            $files[$directory . basename($file->getRealPath(), '.php')] = require $file->getRealPath();
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }
}
