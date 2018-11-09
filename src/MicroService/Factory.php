<?php

namespace CrCms\Foundation\MicroService;

use CrCms\Foundation\MicroService\Contracts\ServiceContract;
use CrCms\Foundation\MicroService\Http\Service as HttpService;
use Illuminate\Contracts\Container\Container;

/**
 * Class Factory
 * @package CrCms\Foundation\MicroService
 */
class Factory
{
    public static function service(Container $app,string $driver): ServiceContract
    {
        switch ($driver) {
            case 'http':
                return new HttpService($app,\CrCms\Foundation\MicroService\Http\Request::capture());
        }
    }
}