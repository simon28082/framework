<?php

namespace CrCms\Foundation\MicroService\Routing\Matching;

use Illuminate\Http\Request;
use CrCms\Foundation\MicroService\Routing\Route;

interface ValidatorInterface
{
    /**
     * Validate a given rule against a route and request.
     *
     * @param  \CrCms\Foundation\MicroService\Routing\Route  $route
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function matches(Route $route, Request $request);
}
