<?php

namespace CrCms\Foundation\App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class ModuleServiceProvider
 * @package CrCms\Foundation\App\Providers
 */
class ModuleServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var string
     */
    protected $name;

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->repositoryListener();

        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapGraphQLRoutes();

        $this->loadMigrationsFrom($this->basePath.'database/migrations');

        $this->loadTranslationsFrom($this->basePath.'resources/lang', $this->name);
    }

    /**
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            $this->basePath ."config/config.php", $this->name
        );
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
        $this->loadRoutesFrom(
            $this->basePath.'routes/web.php'
        );
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
        $this->loadRoutesFrom(
            /*Route::prefix('api')
                ->middleware('api')
                ->group($this->basePath.'routes/api.php')*/
            $this->basePath.'routes/api.php'
        );
    }

    /**
     * @return void
     */
    protected function mapGraphQLRoutes(): void
    {
        if (file_exists($this->basePath.'routes/graphql.php')) {
            require_once $this->basePath.'routes/graphql.php';
        }
    }

    /**
     * @return void
     */
    protected function repositoryListener(): void
    {

    }
}