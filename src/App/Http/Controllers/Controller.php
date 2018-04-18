<?php

namespace CrCms\Foundation\App\Http\Controllers;

use CrCms\Foundation\App\Services\ResponseFactory;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $repository;

    public function __construct()
    {

    }

    protected function response(): ResponseFactory
    {
        return app(ResponseFactory::class);
    }

    public function __get($name)
    {
        if ($name === 'response') {
            return $this->response();
        }
    }
}

