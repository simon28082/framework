<?php

namespace CrCms\Foundation\Rpc\Client;

/**
 * Class Test
 * @package CrCms\Foundation\Rpc\Client
 */
class Test
{

    public function service($service = 'user')
    {
        $driver = app()->make('config')->get('rpc.connections.http.driver');
        $factory = (new Factory(app()))->driver($driver);

        $connection = app('pool.manager')->connection($factory,'http');
    }

}