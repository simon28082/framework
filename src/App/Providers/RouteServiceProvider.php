<?php

namespace CrCms\Foundation\App\Providers;

use CrCms\Foundation\App\WebSocket\WebSocketControllerDispatcher;
use Illuminate\Routing\Contracts\ControllerDispatcher;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{

    public function register()
    {
        parent::register();


        $this->app->bind(\Illuminate\Routing\ControllerDispatcher::class, function ($app) {
            return new WebSocketControllerDispatcher($app);
        });

        $this->app->singleton(ControllerDispatcher::class, function ($app) {
            return new WebSocketControllerDispatcher($app);
        });
    }


}
