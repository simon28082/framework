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

    public function boot()
    {
//        $this->make('pool.manager')
    }

    /**
     *
     */
    public function register()
    {
        $this->registerAlias();

        $this->registerConnectionServices();
    }

    /**
     *
     */
    protected function registerConnectionServices()
    {
        $this->app->singleton('client.manager',function(Application $app){
           return new Manager($app);
        });

        $this->app->singleton('client.factory', function (Application $app) {
            return new Factory($app);
        });
    }

    /**
     *
     */
    protected function registerAlias()
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