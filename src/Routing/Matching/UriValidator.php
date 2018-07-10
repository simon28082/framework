<?php

namespace CrCms\Foundation\Routing\Matching;

use Illuminate\Http\Request;
use Illuminate\Routing\Matching\ValidatorInterface;
use Illuminate\Routing\Route;

class UriValidator implements ValidatorInterface
{
    /**
     * Validate a given rule against a route and request.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function matches(Route $route, Request $request)
    {
        $version = $request->headers->get('X-CRCMS-Media-version','crcms.v1');
        $version = explode('.',$version)[1];

        if ($version !== $route->action['version']) {
            return false;
        }

        $path = $request->path() == '/' ? '/' : $request->path();

        return preg_match($route->getCompiled()->getRegex(), rawurldecode($path));
    }

    protected function versionPath($path,$version)
    {
        return trim(trim($version, '/') . '/' . trim($path, '/'), '/') ?: '/';
    }
}
