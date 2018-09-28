<?php

namespace CrCms\Foundation\MicroService\Server;

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
        $this->mapServiceRoutes();
    }

    /**
     * @return void
     */
    protected function mapServiceRoutes(): void
    {
        $routePath = base_path('routes/service.php');
        file_exists($routePath) && require $routePath;
    }
}
