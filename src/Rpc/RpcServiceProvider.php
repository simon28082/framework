<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/07/02 19:15
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc;

use CrCms\Foundation\Application;
use CrCms\Foundation\Client\Client;
use CrCms\Foundation\Rpc\Contracts\RequestContract;
use CrCms\Foundation\Rpc\Contracts\ResponseContract;
use CrCms\Foundation\Rpc\Contracts\RpcContract;
use CrCms\Foundation\Rpc\Http\Request;
use CrCms\Foundation\Rpc\Http\Response;
use Illuminate\Support\ServiceProvider;

/**
 * Class RpcServiceProvider
 * @package CrCms\Foundation\Rpc
 */
class RpcServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     *
     */
    public function register()
    {
        //暂时直接绑定http，后期扩展再次绑定其它
        $this->app->bind(RequestContract::class, function (Application $app) {
            return (new Request(new Client()))->setRoutePrefix('rpc');
        });

        $this->app->bind(ResponseContract::class, Response::class);

        $this->app->bind(RpcContract::class, Rpc::class);
    }

    /**
     * @return array
     */
    public function provides(): array
    {
        return [
            RequestContract::class,
            ResponseContract::class,
            RpcContract::class,
        ];
    }
}