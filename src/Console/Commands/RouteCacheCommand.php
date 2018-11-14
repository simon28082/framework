<?php

namespace CrCms\Framework\Console\Commands;

use CrCms\Framework\Bootstrap\Start;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Console\RouteCacheCommand as BaseRouteCacheCommand;

/**
 * Class RouteCacheCommand
 * @package CrCms\Framework\Console\Commands
 */
class RouteCacheCommand extends BaseRouteCacheCommand
{
    /**
     * @return \Illuminate\Foundation\Application
     */
    protected function getFreshApplication()
    {
        return tap(Start::instance()->bootstrap()->getApplication(), function ($app) {
            $app->make(Kernel::class)->bootstrap();
        });
    }
}