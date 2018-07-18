<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-07-19 06:40
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\App\Handlers\Traits;

use CrCms\Foundation\App\Services\ResponseFactory;
use InvalidArgumentException;

/**
 * Trait ResponseHandlerTrait
 * @package CrCms\Foundation\App\Handlers\Traits
 */
trait ResponseHandlerTrait
{
    /**
     * @return ResponseFactory
     */
    public function response(): ResponseFactory
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

        throw new InvalidArgumentException("Property not found [{$name}]");
    }
}