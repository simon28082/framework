<?php

namespace CrCms\Foundation\Start\Drivers;

use CrCms\Foundation\StartContract;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Illuminate\Http\Request;

/**
 * Class WebServer
 * @package CrCms\Foundation\Start\Drivers
 */
class Laravel implements StartContract
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