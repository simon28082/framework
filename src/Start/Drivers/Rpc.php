<?php

namespace CrCms\Foundation\Start\Drivers;

use CrCms\Foundation\StartContract;
use Illuminate\Contracts\Container\Container;
use CrCms\Foundation\Rpc\Server\Kernel as HttpKernelContract;
use Illuminate\Http\Request;

/**
 * Class Rpc
 * @package CrCms\Foundation\Start\Drivers
 */
class Rpc implements StartContract
{
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