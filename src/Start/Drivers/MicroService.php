<?php

namespace CrCms\Foundation\Start\Drivers;

use CrCms\Foundation\StartContract;
use Illuminate\Contracts\Container\Container;
use CrCms\Foundation\MicroService\Server\Kernel as HttpKernelContract;
use Illuminate\Http\Request;

/**
 * Class MicroService
 * @package CrCms\Foundation\Start\Drivers
 */
class MicroService implements StartContract
{
    /*public function register(Container $app): void
    {
        // TODO: Implement register() method.
    }*/


    /**
     * @param Container $app
     * @param array $params
     * @return void
     */
    public function run(Container $app, array $params): void
    {
        $kernel = $app->make(HttpKernelContract::class);

        $response = $kernel->handle(
            $request = Request::capture()
        );

        $response->send();

        $kernel->terminate($request, $response);
    }
}