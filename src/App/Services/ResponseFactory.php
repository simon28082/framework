<?php

namespace CrCms\Foundation\App\Services;

use CrCms\Foundation\App\Http\Resources\ResourceCollection;
use Illuminate\Http\JsonResponse;
use CrCms\Foundation\App\Http\Resources\Resource;
use Illuminate\Support\Collection;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use BadMethodCallException;
use InvalidArgumentException;
use JsonSerializable;
use Traversable;
use Illuminate\Contracts\Routing\ResponseFactory as FactoryContract;

/**
 * Class ResponseFactory
 * @package CrCms\Foundation\App\Services
 */
class ResponseFactory
{
    /**
     * @var FactoryContract
     */
    protected $factory;

    /**
     * ResponseFactory constructor.
     * @param FactoryContract $factory
     */
    public function __construct(FactoryContract $factory)
    {
        $this->factory = $factory;
    }

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
     * @param array $fields
     * @param array $includes
     * @return JsonResponse
     */
    public function collection($collection, string $collect = '', array $fields = [], array $includes = []): JsonResponse
    {
        if (!$collection instanceof ResourceCollection && class_exists($collect)) {
            if (substr($collect, -8) === 'Resource') {
                $collection = call_user_func([$collect, 'collection'], $collection);
            } elseif (substr($collect, -10) === 'Collection') {
                $collection = (new $collect($collection));
            } else {
                throw new InvalidArgumentException('Non-existent resource converter');
            }
        }

        if (!$collection instanceof ResourceCollection) {
            throw new InvalidArgumentException('Non-existent resource converter');
        }

        return $this->resourceToResponse($collection, $fields, $includes);
    }

    /**
     * @param $resource
     * @param string $collect
     * @param array $fields
     * @param array $includes
     * @return JsonResponse
     */
    public function resource($resource, string $collect = '', array $fields = [], array $includes = []): JsonResponse
    {
        if (!$resource instanceof Resource && class_exists($collect)) {
            $resource = (new $collect($resource));
        }

        if (!$resource instanceof Resource) {
            throw new InvalidArgumentException('Non-existent resource converter');
        }

        return $this->resourceToResponse($resource, $fields, $includes);
    }

    /**
     * @param $paginator
     * @param string $collect
     * @param array $fields
     * @param array $includes
     * @return JsonResponse
     */
    public function paginator($paginator, string $collect = '', array $fields = [], array $includes = []): JsonResponse
    {
        return $this->collection($paginator, $collect, $fields, $includes);
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
        } elseif (is_object($data)) {
            $data = get_object_vars($data);
        } else {
            throw new InvalidArgumentException('Incorrect parameter format');
        }

        return $this->array([$key => $data]);
    }

    /**
     * @param ResourceCollection|Resource $resource
     * @param array $fields
     * @param array $includes
     * @return JsonResponse
     */
    protected function resourceToResponse($resource, array $fields, array $includes = []): JsonResponse
    {
        if ($includes && $resource instanceof Resource) {
            $resource->setIncludes($includes);
        }

        if (isset($fields['only'])) {
            $type = 'only';
            $fields = $fields['only'];
        } elseif (isset($fields['except']) || isset($fields['hide'])) {
            $type = 'except';
            $fields = $fields['except'] ?? $fields['hide'];
        } else {
            $type = 'except';
        }

        return $resource->$type($fields)->response();
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
    public function __call(string $method, array $parameters)
    {
        if (method_exists($this->factory, $method)) {
            return call_user_func_array([$this->factory, $method], $parameters);
        }

        throw new BadMethodCallException('Undefined method ' . get_class($this) . '::' . $method);
    }
}