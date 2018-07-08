<?php

namespace CrCms\Foundation\App\Services;

use CrCms\Foundation\App\Http\Resources\ResourceCollection;
use Illuminate\Http\JsonResponse;
use CrCms\Foundation\App\Http\Resources\Resource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use BadMethodCallException;
use InvalidArgumentException;
use JsonSerializable;
use Traversable;

class ResponseFactory
{
    /**
     * @param null $location
     * @param null $content
     * @return Response
     */
    public function created($location = null, $content = null): Response
    {
        $response = new Response($content);
        $response->setStatusCode(201);

        if (!is_null($location)) {
            $response->header('Location', $location);
        }

        return $response;
    }

    /**
     * @param null $location
     * @param null $content
     * @return Response
     */
    public function accepted($location = null, $content = null): Response
    {
        $response = new Response($content);
        $response->setStatusCode(202);

        if (!is_null($location)) {
            $response->header('Location', $location);
        }

        return $response;
    }

    /**
     * @return Response
     */
    public function noContent(): Response
    {
        $response = new Response(null);

        return $response->setStatusCode(204);
    }

    /**
     * @param $collection
     * @param string $collect
     * @param array $hide
     * @return Response
     */
    public function collection($collection, string $collect = '', array $hide = []): Response
    {
        if (is_object($collection) && $collection instanceof ResourceCollection) {
            return $collection->hide($hide)->response();
        }

        if (!class_exists($collect)) {
            throw new InvalidArgumentException('Non-existent resource converter');
        }

        if (substr($collect, -8) === 'Resource') {
            return call_user_func([$collect, 'collection'], $collection)->hide($hide)->response();
        } elseif (substr($collect, -10) === 'Collection') {
            return (new $collect($collection))->hide($hide)->response();
        } else {
            throw new InvalidArgumentException('Non-existent resource converter');
        }
    }

    /**
     * @param $resource
     * @param string $collect
     * @param array $hide
     * @return Response
     */
    public function resource($resource, string $collect = '', array $hide = []): Response
    {
        if (is_object($resource) && $resource instanceof Resource) {
            return $resource->hide($hide)->response();
        }

        if (!class_exists($collect)) {
            throw new InvalidArgumentException('Non-existent resource converter');
        }

        return (new $collect($resource))->hide($hide)->response();
    }

    /**
     * @param $paginator
     * @param string $collect
     * @param array $hide
     * @return Response
     */
    public function paginator($paginator, string $collect = '', array $hide = []): Response
    {
        if (is_object($paginator) && $paginator instanceof ResourceCollection) {
            return $paginator->hide($hide)->response();
        }

        if (!class_exists($collect)) {
            throw new InvalidArgumentException('Non-existent resource converter');
        }

        if (substr($collect, -10) === 'Collection') {
            return (new $collect($paginator))->hide($hide)->response();
        } elseif (substr($collect, -8) === 'Resource') {
            return (new ResourceCollection($paginator, $collect))->hide($hide)->response();
        } else {
            throw new InvalidArgumentException('Non-existent resource converter');
        }
    }

    /***
     * @param $message
     * @param $statusCode
     * @throw HttpException
     */
    public function error($message, $statusCode): HttpException
    {
        throw new HttpException($statusCode, $message);
    }

    /**
     * @param string $message
     */
    public function errorNotFound($message = 'Not Found')
    {
        $this->error($message, 404);
    }

    /**
     * Return a 400 bad request error.
     *
     * @param string $message
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return void
     */
    public function errorBadRequest($message = 'Bad Request')
    {
        $this->error($message, 400);
    }

    /**
     * Return a 403 forbidden error.
     *
     * @param string $message
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return void
     */
    public function errorForbidden($message = 'Forbidden')
    {
        $this->error($message, 403);
    }

    /**
     * Return a 500 internal server error.
     *
     * @param string $message
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return void
     */
    public function errorInternal($message = 'Internal Error')
    {
        $this->error($message, 500);
    }

    /**
     * Return a 401 unauthorized error.
     *
     * @param string $message
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return void
     */
    public function errorUnauthorized($message = 'Unauthorized')
    {
        $this->error($message, 401);
    }

    /**
     * Return a 405 method not allowed error.
     *
     * @param string $message
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return void
     */
    public function errorMethodNotAllowed($message = 'Method Not Allowed')
    {
        $this->error($message, 405);
    }

    /**
     * @param array $array
     * @return Response
     */
    public function array(array $array): JsonResponse
    {
        return new JsonResponse($array);
    }

    /**
     * @param array|Collection|JsonSerializable|Traversable $data
     * @param string $key
     * @return JsonResponse
     */
    public function data($data, string $key = 'data'): JsonResponse
    {
        if (is_array($data)) {

        } elseif ($data instanceof Collection) {
            $data = $data->all();
        } elseif ($data instanceof JsonSerializable) {
            $data = $data->jsonSerialize();
        } elseif ($data instanceof Traversable) {
            $data = iterator_to_array($data);
        } else {
            throw new InvalidArgumentException('Incorrect parameter format');
        }

        return $this->array([$key => $data]);
    }

    /**
     * Call magic methods beginning with "with".
     *
     * @param string $method
     * @param array $parameters
     *
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (Str::startsWith($method, 'with')) {
            return call_user_func_array([$this, Str::camel(substr($method, 4))], $parameters);
        }

        throw new BadMethodCallException('Undefined method ' . get_class($this) . '::' . $method);
    }
}