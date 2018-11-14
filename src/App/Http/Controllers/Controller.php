<?php

namespace CrCms\Framework\App\Http\Controllers;

use CrCms\Framework\App\Helpers\InstanceConcern;
use CrCms\Framework\App\Services\ResponseFactory;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use InvalidArgumentException;

/**
 * @property-read ResponseFactory $response
 *
 * Class Controller
 * @package CrCms\Framework\App\Http\Controllers
 */
class Controller extends BaseController
{
    use InstanceConcern, AuthorizesRequests, ValidatesRequests {
        __get as __instanceGet;
    }

    /**
     * @var
     */
    protected $repository;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return ResponseFactory
     */
    protected function response(): ResponseFactory
    {
        return app(ResponseFactory::class);
    }

    /**
     * @param string $name
     * @return ResponseFactory
     */
    public function __get(string $name)
    {
        if ($name === 'response') {
            return $this->response();
        }

        if ((bool)$instance = $this->__instanceGet($name)) {
            return $instance;
        }

        throw new InvalidArgumentException("Property not found [{$name}]");
    }
}

