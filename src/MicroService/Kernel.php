<?php

namespace CrCms\Foundation\MicroService;

use CrCms\Foundation\MicroService\Contracts\ResponseContract;
use CrCms\Foundation\Foundation\Contracts\ApplicationContract;
use CrCms\Foundation\MicroService\Contracts\Kernel as KernelContract;
use CrCms\Foundation\MicroService\Contracts\ServiceContract;
use CrCms\Foundation\MicroService\Routing\Router;
use Exception;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;
use Illuminate\Routing\Pipeline;
use Illuminate\Support\Facades\Facade;
use CrCms\Foundation\MicroService\Contracts\ExceptionHandlerContract as ExceptionHandler;

/**
 * Class Kernel
 * @package CrCms\Foundation\Rpc\Server
 */
class Kernel implements KernelContract
{
    /**
     * The application implementation.
     *
     * @var ApplicationContract|Application
     */
    protected $app;

    /**
     * The router instance.
     *
     * @var \CrCms\Foundation\MicroService\Routing\Router
     */
    protected $router;

    /**
     * @var array
     */
    protected $bootstrappers = [
        \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
        //\Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        //\CrCms\Foundation\MicroService\Bootstrap\HandleExceptions::class,
        \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
        \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
        \Illuminate\Foundation\Bootstrap\BootProviders::class,
    ];

    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \CrCms\Foundation\MicroService\Middleware\HashMiddleware::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * Forces the listed middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
    ];

    /**
     * Create a new HTTP kernel instance.
     *
     * @param  ApplicationContract  $app
     * @param  Router  $router
     * @return void
     */
    public function __construct(ApplicationContract $app, Router $router)
    {
        $this->app = $app;
        $this->router = $router;

        $router->middlewarePriority = $this->middlewarePriority;

        foreach ($this->middlewareGroups as $key => $middleware) {
            $router->middlewareGroup($key, $middleware);
        }

        foreach ($this->routeMiddleware as $key => $middleware) {
            $router->aliasMiddleware($key, $middleware);
        }
    }

    /**
     * @param ServiceContract $service
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws \ReflectionException
     */
    public function handle(ServiceContract $service): ResponseContract
    {
        try {
            $response = $this->sendRequestThroughRouter($service);
        } catch (Exception $e) {
            throw $e;
            $this->reportException($e);

            $response = $this->renderException($request, $e);
        } catch (Throwable $e) {
            throw $e;
            $this->reportException($e = new FatalThrowableError($e));

            $response = $this->renderException($request, $e);
        }

//        $this->app['events']->dispatch(
//            new RequestHandled($request, $response)
//        );

        return $response;
    }

    /**
     * Send the given request through the middleware / router.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendRequestThroughRouter($service)
    {
        $this->app->instance('service', $service);

        Facade::clearResolvedInstance('service');

        $this->bootstrap();

        return (new Pipeline($this->app))
            ->send($service)
            ->through($this->app->shouldSkipMiddleware() ? [] : $this->middleware)
            ->then($this->dispatchToRouter());
    }

    /**
     * Bootstrap the application for HTTP requests.
     *
     * @return void
     */
    public function bootstrap(): void
    {
        if (! $this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }
    }

    /**
     * Get the route dispatcher callback.
     *
     * @return \Closure
     */
    protected function dispatchToRouter()
    {
        return function ($service) {
            $this->app->instance('service', $service);

            return $this->router->dispatch($service);
        };
    }

    /**
     * @param ServiceContract $service
     * @return mixed|void
     */
    public function terminate(ServiceContract $service)
    {
        $this->terminateMiddleware($service);

        $this->app->terminate();
    }

    /**
     * Call the terminate method on any terminable middleware.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    protected function terminateMiddleware(ServiceContract $service)
    {
        $middlewares = $this->app->shouldSkipMiddleware() ? [] : array_merge(
            $this->gatherRouteMiddleware($service),
            $this->middleware
        );

        foreach ($middlewares as $middleware) {
            if (! is_string($middleware)) {
                continue;
            }

            list($name) = $this->parseMiddleware($middleware);

            $instance = $this->app->make($name);

            if (method_exists($instance, 'terminate')) {
                $instance->terminate($service);
            }
        }
    }

    /**
     * Gather the route middleware for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function gatherRouteMiddleware(ServiceContract $service)
    {
        if ($route = $service->route()) {
            return $this->router->gatherRouteMiddleware($route);
        }

        return [];
    }

    /**
     * Parse a middleware string to get the name and parameters.
     *
     * @param  string  $middleware
     * @return array
     */
    protected function parseMiddleware($middleware)
    {
        list($name, $parameters) = array_pad(explode(':', $middleware, 2), 2, []);

        if (is_string($parameters)) {
            $parameters = explode(',', $parameters);
        }

        return [$name, $parameters];
    }

    /**
     * Determine if the kernel has a given middleware.
     *
     * @param  string  $middleware
     * @return bool
     */
    public function hasMiddleware($middleware)
    {
        return in_array($middleware, $this->middleware);
    }

    /**
     * Add a new middleware to beginning of the stack if it does not already exist.
     *
     * @param  string  $middleware
     * @return $this
     */
    public function prependMiddleware($middleware)
    {
        if (array_search($middleware, $this->middleware) === false) {
            array_unshift($this->middleware, $middleware);
        }

        return $this;
    }

    /**
     * Add a new middleware to end of the stack if it does not already exist.
     *
     * @param  string  $middleware
     * @return $this
     */
    public function pushMiddleware($middleware)
    {
        if (array_search($middleware, $this->middleware) === false) {
            $this->middleware[] = $middleware;
        }

        return $this;
    }

    /**
     * Get the bootstrap classes for the application.
     *
     * @return array
     */
    protected function bootstrappers()
    {
        return $this->bootstrappers;
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param  \Exception  $e
     * @return void
     */
    protected function reportException(Exception $e)
    {
        $this->app[ExceptionHandler::class]->report($e);
    }

    /**
     * Render the exception to a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderException(ServiceContract $service, Exception $e)
    {
        return $this->app[ExceptionHandler::class]->render($service, $e);
    }

    /**
     * Get the Laravel application instance.
     *
     * @return ApplicationContract|Application
     */
    public function getApplication(): ApplicationContract
    {
        return $this->app;
    }
}