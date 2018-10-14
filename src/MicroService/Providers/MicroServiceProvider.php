<?php

/**
 * @author simon <simon@crcms.cn>
 * @datetime 2018-10-13 22:50
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\MicroService\Providers;

use CrCms\Foundation\MicroService\Commands\ServerCommand;
use Illuminate\Support\ServiceProvider;

/**
 * Class MicroServiceProvider
 * @package CrCms\Foundation\MicroService\Providers
 */
class MicroServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * @return void
     */
    public function register()
    {
        //bind commands
        $this->app->singleton('command.micro-service', ServerCommand::class);

        // Register commands
        $this->commands(['command.micro-service']);
    }

    /**
     * @return array
     */
    public function provides(): array
    {
        return [
            'command.micro-service',
        ];
    }
}