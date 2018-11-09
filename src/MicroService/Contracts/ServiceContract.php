<?php

namespace CrCms\Foundation\MicroService\Contracts;

use CrCms\Foundation\MicroService\Routing\Route;

/**
 * Class ServiceContract
 * @package CrCms\Foundation\MicroService\Contracts
 */
interface ServiceContract
{
    /**
     * @return string
     */
    public function name(): string;

    /**
     * @param Route $route
     * @return mixed
     */
    public function setRoute(Route $route);

    /**
     * @return Route
     */
    public function getRoute(): Route;

    /**
     * @param RequestContract $request
     * @return mixed
     */
    public function setRequest(RequestContract $request);

    /**
     * @param ResponseContract $response
     * @return mixed
     */
    public function setResponse(ResponseContract $response);

    /**
     * @return RequestContract
     */
    public function getRequest(): RequestContract;

    /**
     * @return ResponseContract
     */
    public function getResponse(): ResponseContract;

    /**
     * @return string
     */
    public static function exceptionHandler() : string ;

    /**
     * @return bool
     */
    public function certification(): bool ;
}