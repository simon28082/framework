<?php

namespace CrCms\Foundation\MicroService\Server\Middleware;

use Illuminate\Http\Request;
use Closure;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class HashMiddleware
 * @package CrCms\Foundation\MicroService\Server\Middleware
 */
class HashMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (empty($request->hasHeader('Authorization'))) {
            throw new UnauthorizedHttpException();
        }

        $hash = hash_hmac('ripemd256', serialize($request->all()), config('micro-service.secret'));
        if ($request->headers->get('Authorization') !== $hash) {
            throw new UnauthorizedHttpException();
        }

        return $next($request);
    }
}