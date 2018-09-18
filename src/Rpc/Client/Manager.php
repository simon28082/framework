<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:14
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Client;

use CrCms\Foundation\Application;

class Manager
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function call(string $name = 'http', array $params = [])
    {
        $driver = $this->app->make('config')->get("rpc.pool");
        $factory = $this->app->make('rpc.client.factory')->driver($name);
        $connection = $this->app->make('pool.manager')->connection($factory,$driver);
        return $connection;
    }




//    /**
//     * @var RequestContract
//     */
//    protected $request;
//
//    /**
//     * Rpc constructor.
//     */
//    public function __construct()
//    {
//        $this->request = app(RequestContract::class);
//    }
//
//    /**
//     * @param string $name
//     * @param array $params
//     * @return ResponseContract
//     */
//    public function call(string $name, array $params = []): ResponseContract
//    {
//        return $this->request->sendPayload($name, $params);
//    }
//
//    /**
//     * @param string $key
//     * @param string $passowrd
//     * @return RpcContract
//     */
//    public function authentication(string $key, string $passowrd = ''): RpcContract
//    {
//    }
//
//    /**
//     * @param string $name
//     * @param array $arguments
//     * @return mixed
//     */
//    public function __call(string $name, array $arguments)
//    {
//        if (method_exists($this->request, $name)) {
//            $result = call_user_func_array([$this->request, $name], $arguments);
//            if ($result instanceof RequestContract) {
//                $this->request = $result;
//                return $this;
//            }
//
//            return $result;
//        }
//
//        throw new BadMethodCallException("The method[{$name}] is not exists");
//    }
}