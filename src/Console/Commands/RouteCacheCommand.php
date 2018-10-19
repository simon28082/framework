<?php

namespace CrCms\Foundation\Console\Commands;

use CrCms\Foundation\Application;
use CrCms\Foundation\Start;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Console\RouteCacheCommand as BaseRouteCacheCommand;

/**
 * Class RouteCacheCommand
 * @package CrCms\Foundation\Console\Commands
 */
class RouteCacheCommand extends BaseRouteCacheCommand
{
    /**
     * @return \Illuminate\Foundation\Application|Application
     */
    protected function getFreshApplication()
    {
        return tap(Start::instance()->bootstrap()->getApp(), function ($app) {
            $app->make(Kernel::class)->bootstrap();
        });
    }
}