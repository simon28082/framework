<?php

namespace CrCms\Foundation\Rpc\Client;

use CrCms\Foundation\Client\Manager;
use CrCms\Foundation\Rpc\Contracts\Selector;
use CrCms\Foundation\Rpc\Contracts\ServiceDiscoverContract;
use Illuminate\Foundation\Application;
use Exception;

/**
 * Class ServiceDiscovery
 * @package CrCms\Foundation\Rpc\Client
 */
class ServiceDiscover implements ServiceDiscoverContract
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $services = [];

    /**
     * @var Selector
     */
    protected $selector;

    /**
     * @var Manager
     */
    protected $client;

    /**
     * ServiceDiscover constructor.
     * @param Application $app
     * @param Selector $selector
     * @param Manager $manager
     */
    public function __construct(Application $app, Selector $selector, Manager $manager)
    {
        $this->app = $app;
        $this->selector = $selector;
        $this->client = $manager;
    }

    /**
     * @param string $service
     * @return array
     * @throws Exception
     */
    public function discover(string $service): array
    {
        if (empty($this->services[$service])) {
            $this->services[$service] = $this->services($service);
        }

        return $this->selector->select($this->services[$service]);
    }

    /**
     * @param string $service
     * @return array
     * @throws Exception
     */
    protected function services(string $service): array
    {
        $config = $this->app->make('config')->get("rpc.connections.consul.discovery");
        $this->client->connection('consul');
        try {
            $content = $this->client->request($config['uri'] . '/' . $service, ['method' => 'get'])->getContent();
            return collect($content)->mapWithKeys(function ($item) {
                return [$item['ServiceID'] => $item];
            })->toArray();
        } catch (Exception $exception) {
            throw $exception;
        } finally {
            $this->client->close();
        }
    }
}