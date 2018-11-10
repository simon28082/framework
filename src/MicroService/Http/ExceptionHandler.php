<?php

/**
 * @author simon <simon@crcms.cn>
 * @datetime 2018-11-09 20:04
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\MicroService\Http;

use CrCms\Foundation\MicroService\Contracts\ExceptionHandlerContract;
use CrCms\Foundation\MicroService\Contracts\ServiceContract;
use CrCms\Foundation\MicroService\Exceptions\ExceptionHandler as BaseExceptionHandler;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use CrCms\Foundation\MicroService\Http\Response;
use Exception as BaseException;

/**
 * Class ExceptionHandler
 * @package CrCms\Foundation\MicroService
 */
class ExceptionHandler extends BaseExceptionHandler implements ExceptionHandlerContract
{
    /**
     * @param ServiceContract $service
     * @param BaseException $e
     * @return Response|\Illuminate\Http\JsonResponse|null|\Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\Response\
     */
    public function render(ServiceContract $service, BaseException $e)
    {
        if (method_exists($e, 'render') && $response = $e->render($service)) {
            return new Response($response);
        } elseif ($e instanceof Responsable) {
            return $e->toResponse($service->getRequest());
        }

        $e = $this->prepareException($e);

        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } elseif ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $service);
        }

        return $this->prepareJsonResponse($service, $e);
    }

    /**
     * Determine if the given exception is an HTTP exception.
     *
     * @param BaseException $e
     * @return bool
     */
    protected function isHttpException(BaseException $e): bool
    {
        return $e instanceof HttpExceptionInterface;
    }

    /**
     * Convert the given exception to an array.
     *
     * @param  \Exception $e
     * @return array
     */
    protected function convertExceptionToArray(Exception $e)
    {
        return config('app.debug') ? [
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->map(function ($trace) {
                return Arr::except($trace, ['args']);
            })->all(),
        ] : [
            'message' => $this->isHttpException($e) ? $e->getMessage() : 'Server Error',
        ];
    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function prepareJsonResponse(ServiceContract $service, Exception $e)
    {
        return new Response(
            $this->convertExceptionToArray($e),
            $this->isHttpException($e) ? $e->getStatusCode() : 500,
            $this->isHttpException($e) ? $e->getHeaders() : [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param ValidationException $e
     * @param ServiceContract $service
     * @return Response|null|\Symfony\Component\HttpFoundation\Response\
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, ServiceContract $service)
    {
        if ($e->response) {
            return $e->response;
        }

        return new Response([
            'message' => $exception->getMessage(),
            'errors' => $exception->errors(),
        ], $exception->status);
    }

    /**
     * Prepare exception for rendering.
     *
     * @param  \Exception $e
     * @return \Exception
     */
    protected function prepareException(Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }

        return $e;
    }
}