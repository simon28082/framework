<?php

namespace CrCms\Foundation\MicroService\Routing\Events;

use CrCms\Foundation\MicroService\Contracts\ServiceContract;
use CrCms\Foundation\MicroService\Routing\Route;

class RouteMatched
{
    /**
     * The route instance.
     *
     * @var \CrCms\Foundation\MicroService\Routing\Route
     */
    public $route;

    /**
     * @var ServiceContract
     */
    public $service;

    public function __construct(Route $route, ServiceContract $service)
    {
        $this->route = $route;
        $this->service = $service;
    }
}
