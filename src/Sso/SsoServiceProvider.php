<?php

/**
 * @author simon <simon@crcms.cn>
 * @datetime 2018-08-15 07:33
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Sso\Client;

use CrCms\Foundation\Rpc\Client;
use CrCms\Foundation\Sso\Client\Contracts\InteractionContract;
use Illuminate\Support\ServiceProvider;

/**
 * Class SsoServiceProvider
 * @package CrCms\Foundation\Sso\Client
 */
class SsoServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    public $defer = true;

    /**
     *
     */
    public function register()
    {
        $this->app->bind(InteractionContract::class, function ($app) {
            return new DefaultInteractor($app->make(Client\Rpc::class));
        });
    }

    /**
     * @return array
     */
    public function provides(): array
    {
        return [InteractionContract::class];
    }
}