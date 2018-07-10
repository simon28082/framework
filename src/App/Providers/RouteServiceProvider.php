<?php

namespace CrCms\Foundation\App\Providers;

use CrCms\Foundation\App\WebSocket\WebSocketControllerDispatcher;
use Illuminate\Routing\Contracts\ControllerDispatcher;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->mapRpcRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes(): void
    {
        $routePath = base_path('routes/web.php');
        file_exists($routePath) && require $routePath;
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes(): void
    {
        $routePath = base_path('routes/api.php');
        file_exists($routePath) && require $routePath;
    }

    /**
     * @return void
     */
    protected function mapRpcRoutes(): void
    {
        $routePath = base_path('routes/rpc.php');
        file_exists($routePath) && require $routePath;
    }

    /**
     * @return void
     */
    public function register(): void
    {
        parent::register();


        /*$this->app->bind(\Illuminate\Routing\ControllerDispatcher::class, function ($app) {
            return new WebSocketControllerDispatcher($app);
        });

        $this->app->singleton(ControllerDispatcher::class, function ($app) {
            return new WebSocketControllerDispatcher($app);
        });*/
    }
}
