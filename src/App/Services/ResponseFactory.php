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
     * @param ResourceCollection $collection
     * @return JsonResponse
     */
    public function collection(ResourceCollection $collection): JsonResponse
    {
        return $collection->response();
    }

    /**
     * @param Resource $resource
     * @return JsonResponse
     */
    public function resource(Resource $resource): JsonResponse
    {
        return $resource->response();
    }

    /**
     * @param ResourceCollection $paginator
     * @return JsonResponse
     */
    public function paginator(ResourceCollection $paginator): JsonResponse
    {
        return $this->collection($paginator);
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
    public function __call(string $method, array $parameters)
    {
        if (method_exists($this->factory, $method)) {
            return call_user_func_array([$this->factory, $method], $parameters);
        }

        throw new BadMethodCallException('Undefined method ' . get_class($this) . '::' . $method);
    }
}