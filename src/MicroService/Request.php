<?php

namespace CrCms\Foundation\MicroService;

use CrCms\Foundation\MicroService\Contracts\RequestContract;

/**
 * Class Request
 * @package CrCms\Foundation\MicroService
 */
class Request
{
    protected $request;

    protected $name;

    public function __construct( $request,string $name = '')
    {
        $this->request = $request;
        $this->name = $name;
    }

    public function name()
    {
        return $this->name;
    }
}