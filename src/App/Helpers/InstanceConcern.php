<?php

/**
 * @author simon <simon@crcms.cn>
 * @datetime 2018-08-12 13:55
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\App\Helpers;

use CrCms\Foundation\Swoole\Server\AbstractServer;
use CrCms\Foundation\Transporters\Contracts\DataProviderContract;
use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Str;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Bus\Dispatcher;
use Swoole\Server;
use InvalidArgumentException;

/**
 * @property-read Container $app
 * @property-read Config $config
 * @property-read Cache $cache
 * @property-read AuthFactory $auth
 * @property-read Dispatcher $dispatcher
 * @property-read Guard $guard
 * @property-read DataProviderContract $data
 * @property-read AbstractServer|Server|\Swoole\Http\Server|\Swoole\WebSocket\Server $server
 *
 * Trait InstanceConcern
 * @package CrCms\Foundation\App\Helpers
 */
trait InstanceConcern
{
    /**
     * @return Container
     */
    public function app(): Container
    {
        return Container::getInstance();
    }

    /**
     * @return Config
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function config(): Config
    {
        return $this->app->make(Config::class);
    }

    /**
     * @return Cache
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function cache(): Cache
    {
        return $this->app->make(Cache::class);
    }

    /**
     * @return AuthFactory
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function auth(): AuthFactory
    {
        return $this->app->make(AuthFactory::class);
    }

    /**
     * @return Dispatcher
     */
    public function dispatcher(): Dispatcher
    {
        return $this->app->make(Dispatcher::class);
    }

    /**
     * @param string $guard
     * @return Guard
     */
    public function guard(): Guard
    {
        return $this->auth->guard($this->config->get('auth.defaults.guard'));
    }

    /**
     * @return DataProviderContract
     */
    public function data(): DataProviderContract
    {
        return $this->app->make('data.provider');
    }

    /**
     * @return AbstractServer
     */
    public function server(): AbstractServer
    {
        $serverApplication = $this->app->getServerApplication();

        if ($serverApplication instanceof AbstractServer) {
            return $serverApplication->getServer();
        }

        $class = get_class($serverApplication);
        throw new InvalidArgumentException("The server application[{$class}] is not instanceof AbstractServer");
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        $name = Str::camel($name);

        if (method_exists($this, Str::camel($name))) {
            return $this->{$name}();
        }

        return null;
    }
}