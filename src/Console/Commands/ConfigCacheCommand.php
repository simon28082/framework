<?php

namespace CrCms\Framework\Console\Commands;

use CrCms\Framework\Start;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Console\ConfigCacheCommand as BaseConfigCacheCommand;

/**
 * Class ConfigCacheCommand
 * @package CrCms\Framework\Console\Commands
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
        $app = tap(Start::instance()->bootstrap()->getApplication(), function ($app) {
            $app->make(Kernel::class)->bootstrap();
        });

        return $app['config']->all();
    }
}