<?php

/**
 * @author simon <simon@crcms.cn>
 * @datetime 2018-08-15 07:09
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Sso\Client;

use GuzzleHttp\Client;
use Illuminate\Contracts\Container\Container;

/**
 * Class Manager
 * @package CrCms\Foundation\Sso\Client
 */
class Manager
{
    protected $interactors = [];

    protected $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    public function interactor()
    {

    }

    public function defaultInteractor()
    {
        return $this->app->make('config')->get('sso.client.interactor');
    }


    protected function createDefaultInteractor()
    {
        return new DefaultInteractor(new Client([
            'base_uri'=>$this->app->make('config')->get('sso.client.server'),
            'timeout' => 2,
        ]));
    }


    public function __call(string $name, $arguments)
    {
        // TODO: Implement __call() method.
    }

}