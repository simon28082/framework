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
        $token = $request->headers->get('Authorization');
        $hash = hash_hmac('ripemd256', serialize($request->all()), config('micro-service.secret'));
        if ($token !== $hash) {
            throw new UnauthorizedHttpException($token);
        }

        return $next($request);
    }
}