<?php

namespace CrCms\Foundation\Routing;

use Closure;
use BadMethodCallException;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Routing\RouteRegistrar as BaseRouteRegistrar;

class RouteRegistrar extends BaseRouteRegistrar
{
    /**
     * The attributes that can be set through this class.
     *
     * @var array
     */
    protected $allowedAttributes = [
        'as', 'domain', 'middleware', 'name', 'namespace', 'prefix', 'version'
    ];
}
