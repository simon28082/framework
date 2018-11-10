<?php

namespace CrCms\Foundation\MicroService\Http;

use CrCms\Foundation\MicroService\Contracts\RequestContract;
use CrCms\Foundation\MicroService\Contracts\ResponseContract;
use CrCms\Foundation\MicroService\Contracts\ServiceContract;
use CrCms\Foundation\MicroService\Routing\Route;
use Illuminate\Contracts\Container\Container;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
}