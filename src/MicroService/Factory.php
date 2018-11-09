<?php

namespace CrCms\Foundation\MicroService;

/**
 * Class Factory
 * @package CrCms\Foundation\MicroService
 */
class Factory
{
    public static function request()
    {
        $default = config('micro-service')->get('default');
        switch ($default) {
            case 'http':
                return \Illuminate\Http\Request::capture();
        }
    }

    public static function response()
    {

    }
}