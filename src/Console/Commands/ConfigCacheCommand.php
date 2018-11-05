<?php

namespace CrCms\Foundation\Console\Commands;

use CrCms\Foundation\Application;
use CrCms\Foundation\Start;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Console\ConfigCacheCommand as BaseConfigCacheCommand;

/**
 * Class ConfigCacheCommand
 * @package CrCms\Foundation\Console\Commands
 */
class ConfigCacheCommand extends BaseConfigCacheCommand
{
    /**
     * Boot a fresh copy of the application configuration.
     *
     * @return array
     */
    protected function getFreshConfiguration()
    {
        $app = tap(Start::instance()->bootstrap()->getApp(), function ($app) {
            $app->make(Kernel::class)->bootstrap();
        });

        return $app['config']->all();
    }
}