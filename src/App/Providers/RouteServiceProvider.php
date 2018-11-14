<?php

namespace CrCms\Framework\App\Providers;

use CrCms\Framework\App\WebSocket\WebSocketControllerDispatcher;
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
}
