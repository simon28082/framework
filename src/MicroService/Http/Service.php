<?php

namespace CrCms\Foundation\MicroService\Http;

use CrCms\Foundation\Foundation\Contracts\ApplicationContract;
use CrCms\Foundation\MicroService\Contracts\ExceptionHandlerContract;
use CrCms\Foundation\MicroService\Contracts\RequestContract;
use CrCms\Foundation\MicroService\Contracts\ResponseContract;
use CrCms\Foundation\MicroService\Contracts\ServiceContract;
use CrCms\Foundation\MicroService\Routing\Route;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class Service
 * @package CrCms\Foundation\MicroService\Http
 */
class Service implements ServiceContract
{
    protected $request;

    protected $response;

    protected $route;

    protected $app;

    public function __construct(Container $app,RequestContract $request)
    {
        $this->app = $app;
        $this->setRequest($request);
    }

    public function setRoute(Route $route)
    {
        $this->route = $route;
    }

    public function getRoute(): Route
    {
        return $this->route;
    }

    public static function exceptionHandler(): string
    {
        return ExceptionHandler::class;
    }

    public function certification(): bool
    {
        $token = $this->request->headers->get('X-CRCMS-Microservice-Hash');
        $hash = hash_hmac('ripemd256', serialize($this->request->all()), config('ms.secret'));
        if ($token !== $hash) {
            throw new AccessDeniedHttpException("Microservice Hash error:" . strval($token));
        }

        return true;
    }

    /**
     * Get the route resolver callback.
     *
     * @return \Closure
     */
    public function getRouteResolver()
    {
        return $this->routeResolver ?: function () {
            //
        };
    }

    /**
     * Set the route resolver callback.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function setRouteResolver(Closure $callback)
    {
        $this->routeResolver = $callback;

        return $this;
    }

    public function setRequest(RequestContract $request)
    {
        $this->request = $request;
    }

    public function setResponse(ResponseContract $response)
    {
        $this->response = $response;
    }

    public function getRequest(): RequestContract
    {
        return $this->request;
    }

    public function getResponse(): ResponseContract
    {
        return $this->response;
    }

    public function name(): string
    {
        return $this->request->get('method');
    }

//
//    public function response($response)
//    {
//        if ($response instanceof Responsable) {
//            $response = $response->toResponse($this->request);
//        }
//
//        if ($response instanceof PsrResponseInterface) {
//            $response = (new HttpFoundationFactory)->createResponse($response);
//        } elseif ($response instanceof Model && $response->wasRecentlyCreated) {
//            $response = new JsonResponse($response, 201);
//        } elseif (!$response instanceof SymfonyResponse &&
//            ($response instanceof Arrayable ||
//                $response instanceof Jsonable ||
//                $response instanceof ArrayObject ||
//                $response instanceof JsonSerializable ||
//                is_array($response))) {
//            $response = new JsonResponse($response);
//        } elseif (!$response instanceof SymfonyResponse) {
//            $response = new Response($response);
//        }
//
//        if ($response->getStatusCode() === Response::HTTP_NOT_MODIFIED) {
//            $response->setNotModified();
//        }
//
//        return $response->prepare($this->request);
//    }
}