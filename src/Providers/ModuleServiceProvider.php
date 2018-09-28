<?php

namespace CrCms\Foundation\Providers;

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

        $this->mapServiceRoutes();

        $this->loadMigrationsFrom($this->basePath . 'database/migrations');

        $this->loadTranslationsFrom($this->basePath . 'resources/lang', $this->name);
    }

    /**
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            $this->basePath . "config/config.php", $this->name
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
        $file = $this->basePath . 'routes/web.php';

        if (file_exists($file)) {
            $this->loadRoutesFrom($file);
        }
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
        $file = $this->basePath . 'routes/api.php';

        if (file_exists($file)) {
            $this->loadRoutesFrom(
            /*Route::prefix('api')
                ->middleware('api')
                ->group($file)*/
                $file
            );
        }
    }

    /**
     * @return void
     */
    protected function mapServiceRoutes(): void
    {
        $file = $this->basePath . 'routes/service.php';

        if (file_exists($file)) {
            $this->loadRoutesFrom($file);
        }
    }

    /**
     * @return void
     */
    protected function mapGraphQLRoutes(): void
    {
        $file = $this->basePath . 'routes/graphql.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }

    /**
     * @return void
     */
    protected function repositoryListener(): void
    {
    }
}