<?php

namespace CrCms\Foundation\MicroService\Http;

use CrCms\Foundation\Foundation\Contracts\ApplicationContract;
//use CrCms\Foundation\MicroService\Contracts\ExceptionHandlerContract;
use CrCms\Foundation\MicroService\Contracts\RequestContract;
use CrCms\Foundation\MicroService\Contracts\ResponseContract;
use CrCms\Foundation\MicroService\Contracts\ServiceContract;
use CrCms\Foundation\MicroService\Routing\Route;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\Routing\BindingRegistrar;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarContract;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use CrCms\Foundation\MicroService\Http\Response as HttpResponse;

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

    protected $indexes;

    public function __construct(Container $app, RequestContract $request)
    {
        $this->app = $app;
        $this->setRequest($request);
    }

    public function setRoute(Route $route): ServiceContract
    {
        $this->route = $route;
        return $this;
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
            return false;
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
     * @param  \Closure $callback
     * @return $this
     */
    public function setRouteResolver(Closure $callback)
    {
        $this->routeResolver = $callback;

        return $this;
    }

    public function setRequest(RequestContract $request): ServiceContract
    {
        $this->request = $request;
        return $this;
    }

    public function setResponse($response): ServiceContract
    {
        $this->response = HttpResponse::createReponse($response);
        return $this;
    }



//    public function setResponse($response): ServiceContract
//    {
//        if ($response instanceof Model && $response->wasRecentlyCreated) {
//            $response = new Response($response, 201);
//        } elseif ($response instanceof JsonResponse) {
//            $response = new Response($response->getData(), $response->getStatusCode(), $response->headers, $response->getEncodingOptions());
//        } elseif (!$response instanceof SymfonyResponse &&
//            ($response instanceof Arrayable ||
//                $response instanceof Jsonable ||
//                $response instanceof ArrayObject ||
//                $response instanceof JsonSerializable ||
//                is_array($response))) {
//            $response = new Response($response);
//        } else {
//            $response = new Response($response);
//        }
//
//        if ($response->getStatusCode() === Response::HTTP_NOT_MODIFIED) {
//            $response->setNotModified();
//        }
//
//        $this->response = $response;
//        return $this;
//    }

//    public static function createResponse($response)
//    {
//        if ($response instanceof Model && $response->wasRecentlyCreated) {
//            $response = new Response($response, 201);
//        } elseif ($response instanceof JsonResponse) {
//            $response = new Response($response->getData(), $response->getStatusCode(), $response->headers, $response->getEncodingOptions());
//        } elseif (!$response instanceof SymfonyResponse &&
//            ($response instanceof Arrayable ||
//                $response instanceof Jsonable ||
//                $response instanceof ArrayObject ||
//                $response instanceof JsonSerializable ||
//                is_array($response))) {
//            $response = new Response($response);
//        } else {
//            $response = new Response($response);
//        }
//
//        if ($response->getStatusCode() === Response::HTTP_NOT_MODIFIED) {
//            $response->setNotModified();
//        }
//
//        return $response;
//    }

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

    public function indexes(?string $key = null)
    {
        if (is_null($this->indexes)) {
            $method = explode('.', $this->request->get('method'));
            $this->indexes = ['name' => $method[0], 'method' => $method[1] ?? null];
        }

        return $this->indexes[$key];
    }

    public static function toResponse(RequestContract $request, ResponseContract $response): ResponseContract
    {
        return $response->prepare($request);
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