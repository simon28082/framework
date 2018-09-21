<?php

namespace CrCms\Foundation\Rpc\Client\Drivers;

use CrCms\Foundation\Client\Manager;
use CrCms\Foundation\Rpc\Contracts\ResponseContract;
use CrCms\Foundation\Rpc\Contracts\RpcContract;

/**
 * Class Http
 * @package CrCms\Foundation\Rpc\Client\Drivers
 */
class Http implements RpcContract
{
    /**
     * @var array
     */
    protected $headers = [
        'User-Agent' => 'CRCMS-JSON-RPC PHP Client',
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];

    protected $method = 'post';

    protected $client;

    protected $config;

    public function __construct(Manager $manager, array $config = [])
    {
        $this->client = $manager;
        $this->config = $config;
        $this->addHeaders($config['header'] ?? []);
    }

    public function setMethod(string $method)
    {
        $this->method = $method;

        return $this;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function addHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    public function call(array $service, string $uri, array $params = []): Manager
    {
        //@todo 这里就有问题了，新服务的配置怎么传入,并不是在client的配置文件里面的，而是动态加载的
        //@todo 解决上面的问题，暂时修改了Manage里面的connection,支持动态化数组传入

        return $this->client->connection([
            'name' => 'rpc_' . $service['ServiceName'],
            'driver' => 'http',
            'host' => $service['ServiceAddress'],
            'port' => $service['ServicePort'],
            'settings' => [
                'timeout' => 1,
                //'ssl' => env('PASSPORT_SSL', true),
            ],
        ])->request($uri, ['method' => $this->method, 'payload' => $params]);
    }

    public function authentication(string $key, string $passowrd = ''): RpcContract
    {
        // TODO: Implement authentication() method.
    }


}