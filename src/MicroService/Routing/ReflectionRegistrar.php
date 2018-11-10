<?php

namespace CrCms\Foundation\MicroService\Routing;

/**
 * Class ReflectionRegistrar
 * @package CrCms\Foundation\MicroService\Routing
 */
class ReflectionRegistrar
{
    /**
     * Create a new resource registrar instance.
     *
     * @param  \CrCms\Foundation\MicroService\Routing\Router  $router
     * @return void
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function register($name, $action, array $options = [])
    {
//        $controller =
        dd($name,$action);
    }

}