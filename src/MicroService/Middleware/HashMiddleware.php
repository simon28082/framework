<?php

namespace CrCms\Foundation\MicroService\Middleware;

use CrCms\Foundation\MicroService\Contracts\ServiceContract;
use Illuminate\Http\Request;
use Closure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class HashMiddleware
 * @package CrCms\Foundation\MicroService\Middleware
 */
class HashMiddleware
{
    /**
     * @param ServiceContract $service
     * @param Closure $next
     * @return mixed
     */
    public function handle(ServiceContract $service, Closure $next)
    {
//        $token = $service->token();
//        $hash = hash_hmac('ripemd256', serialize($request->all()), config('ms.secret'));
//        if ($token !== $hash) {
//            throw new AccessDeniedHttpException("Microservice Hash error:" . strval($token));
//        }
//        $service->certification();


        return $next($service);
    }
}