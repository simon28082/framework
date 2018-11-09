<?php

namespace CrCms\Foundation\MicroService\Http;

use CrCms\Foundation\MicroService\Contracts\ServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Class Service
 * @package CrCms\Foundation\MicroService\Http
 */
class Service implements ServiceContract
{
    protected $request;

    protected $response;

    public function __construct()
    {
        $this->request = \Illuminate\Http\Request::capture();
    }

    public function request()
    {
        return $this->request;
    }

    public function currentName(): string
    {
        return $this->request->get('method');
    }

    public function response($response)
    {
        if ($response instanceof Responsable) {
            $response = $response->toResponse($this->request);
        }

        if ($response instanceof PsrResponseInterface) {
            $response = (new HttpFoundationFactory)->createResponse($response);
        } elseif ($response instanceof Model && $response->wasRecentlyCreated) {
            $response = new JsonResponse($response, 201);
        } elseif (!$response instanceof SymfonyResponse &&
            ($response instanceof Arrayable ||
                $response instanceof Jsonable ||
                $response instanceof ArrayObject ||
                $response instanceof JsonSerializable ||
                is_array($response))) {
            $response = new JsonResponse($response);
        } elseif (!$response instanceof SymfonyResponse) {
            $response = new Response($response);
        }

        if ($response->getStatusCode() === Response::HTTP_NOT_MODIFIED) {
            $response->setNotModified();
        }

        return $response->prepare($this->request);
    }
}