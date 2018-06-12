<?php

namespace CrCms\Foundation\Bootstrap;

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
        $files = $this->getConfigurationFiles($app);

        $frameworkFiles = $this->getFrameworkConfigFiles($app);

        if (!isset($files['app']) && !isset($frameworkFiles['app'])) {
            throw new Exception('Unable to load the "app" configuration file.');
        }

        $this->setIntersectConfig($repository, $frameworkFiles, $files);

        $this->setDiffConfig($repository, $frameworkFiles, $files);
    }

    /**
     * @param RepositoryContract $repository
     * @param array $frameworkFiles
     * @param array $files
     * @return void
     */
    protected function setIntersectConfig(RepositoryContract $repository, array $frameworkFiles, array $files)
    {
        $intersectKey = array_intersect_key($frameworkFiles, $files);
        foreach ($intersectKey as $key => $path) {
            $tmpArray = require $frameworkFiles[$key];
            $repository->set($key, array_merge_recursive_adv($tmpArray, require $files[$key]));
            unset($tmpArray);
        }
    }

    /**
     * @param RepositoryContract $repository
     * @param array $frameworkFiles
     * @param array $files
     * @return void
     */
    protected function setDiffConfig(RepositoryContract $repository, array $frameworkFiles, array $files)
    {
        $diffKey = array_diff_key($frameworkFiles, $files);
        foreach ($diffKey as $key => $path) {
            $repository->set($key, require $path);
        }
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return array
     */
    protected function getFrameworkConfigFiles(\CrCms\Foundation\Application $app)
    {
        $files = [];

        $configPath = realpath($app->frameworkConfigPath());

        foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file) {
            $directory = $this->getNestedDirectory($file, $configPath);

            $files[$directory . basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }

}
