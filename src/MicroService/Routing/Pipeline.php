<?php

namespace CrCms\Foundation\MicroService\Routing;

use Closure;
use CrCms\Foundation\MicroService\Contracts\ServiceContract;
use Exception;
use Throwable;
use CrCms\Foundation\MicroService\Contracts\ExceptionHandlerContract as ExceptionHandler;
use Illuminate\Routing\Pipeline as BasePipeline;
use Symfony\Component\Debug\Exception\FatalThrowableError;

/**
 * This extended pipeline catches any exceptions that occur during each slice.
 *
 * The exceptions are converted to HTTP responses for proper middleware handling.
 */
class Pipeline extends BasePipeline
{
    /**
     * Handle the given exception.
     *
     * @param  mixed  $passable
     * @param  \Exception  $e
     * @return mixed
     *
     * @throws \Exception
     */
    protected function handleException($passable, Exception $e)
    {
        if (! $this->container->bound(ExceptionHandler::class) ||
            ! $passable instanceof ServiceContract) {
            throw $e;
        }

        $handler = $this->container->make(ExceptionHandler::class);

        $handler->report($e);

        $response = $handler->render($passable, $e);

        if (method_exists($response, 'withException')) {
            $response->withException($e);
        }

        return $response;
    }
}
