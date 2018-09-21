<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/28 20:42
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client;

use CrCms\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * Class ClientServiceProvider
 * @package CrCms\Foundation\Client
 */
class ClientServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * @return void
     */
    public function register(): void
    {
        $this->registerAlias();

        $this->registerServices();
    }

    /**
     * @return void
     */
    protected function registerServices(): void
    {
        $this->app->singleton('client.manager', function (Application $app) {
            return new Manager($app);
        });

        $this->app->singleton('client.factory', function (Application $app) {
            return new Factory($app);
        });
    }

    /**
     * @return void
     */
    protected function registerAlias(): void
    {
        $this->app->alias('client.factory', Factory::class);
        $this->app->alias('client.manager', Manager::class);
    }

    /**
     * @return array
     */
    public function provides(): array
    {
        return [
            'client.manager',
            'client.factory',
        ];
    }
}