<?php

/**
 * @author simon <simon@crcms.cn>
 * @datetime 2018-10-13 22:50
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Http\Providers;

use CrCms\Foundation\Http\Commands\ServerCommand;
use Illuminate\Support\ServiceProvider;

/**
 * Class HttpServiceProvider
 * @package CrCms\Foundation\MicroService\Providers
 */
class HttpServiceProvider extends ServiceProvider
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
        $this->app->singleton('command.http', ServerCommand::class);

        // Register commands
        $this->commands(['command.http']);
    }

    /**
     * @return array
     */
    public function provides(): array
    {
        return [
            'command.http',
        ];
    }
}