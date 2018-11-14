<?php

namespace CrCms\Framework\App\WebSocket;

use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\Contracts\ControllerDispatcher as ControllerDispatcherContract;
use Illuminate\Routing\RouteDependencyResolverTrait;
use Illuminate\Routing\Route;

/**
 * Class WebSocketControllerDispatcher
 * @package CrCms\Framework\App\WebSocket
 */
class WebSocketControllerDispatcher implements ControllerDispatcherContract
{
    use RouteDependencyResolverTrait;

    /**
     * The container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    protected static $frame = null;

    /**
     * Create a new controller dispatcher instance.
     *
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public static function setFrame($frame)
    {
        static::$frame = $frame;
    }

    /**
     * Dispatch a request to a given controller and method.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  mixed  $controller
     * @param  string  $method
     * @return mixed
     */
    public function dispatch(Route $route, $controller, $method)
    {
        $parameters = $this->resolveClassMethodDependencies(
            $route->parametersWithoutNulls(), $controller, $method
        );
//dump('websok');
//        if (!empty(static::$frame)) {
//            $parameters[0] = static::$frame;
//            static::$frame = null;
//        }

        if (method_exists($controller,'setFrame') && !is_null(static::$frame)) {
            $controller->setFrame(static::$frame);
            static::$frame = null;
        }

        if (method_exists($controller, 'callAction')) {
            return $controller->callAction($method, $parameters);
        }





        return $controller->{$method}(...array_values($parameters));
    }

    /**
     * Get the middleware for the controller instance.
     *
     * @param  \Illuminate\Routing\Controller  $controller
     * @param  string  $method
     * @return array
     */
    public function getMiddleware($controller, $method)
    {
        if (! method_exists($controller, 'getMiddleware')) {
            return [];
        }

        return collect($controller->getMiddleware())->reject(function ($data) use ($method) {
            return static::methodExcludedByOptions($method, $data['options']);
        })->pluck('middleware')->all();
    }

    /**
     * Determine if the given options exclude a particular method.
     *
     * @param  string  $method
     * @param  array  $options
     * @return bool
     */
    protected static function methodExcludedByOptions($method, array $options)
    {
        return (isset($options['only']) && ! in_array($method, (array) $options['only'])) ||
            (! empty($options['except']) && in_array($method, (array) $options['except']));
    }
}