<?php

namespace CrCms\Foundation\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BackstageManagement
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        define('BACKSTAGE_MANAGEMENT', 1);

        return $next($request);
    }
}