<?php

namespace CrCms\Foundation\MicroService\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

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
        if (file_exists($routePath)) {
            Route::middleware('micro_service')
                ->group($routePath);
        }
    }
}
