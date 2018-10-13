<?php

namespace CrCms\Foundation\MicroService\Commands;

use CrCms\Foundation\Client\Manager;
use Illuminate\Console\Command;
use Exception;

/**
 * Class ServiceRegisterCommand
 * @package CrCms\Foundation\MicroService\Commands
 */
class ServiceRegisterCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'micro-service:register {name: The micro service connections driver}';

    /**
     * @return void
     */
    public function handle(): void
    {
        $name = $this->argument('name');

        $config = $this->config($name);
        $client = $this->client();
        try {
            $client->connection($name)
                ->request(
                    array_get($config, 'register.uri'),
                    ['payload' => array_except($config['register'], ['uri']), 'method' => 'put']
                );
            $response = $client->getResponse();
            if ($response->getStatusCode() === 200) {
                $this->info("Service register successful.");
            } else {
                $this->error('Service register failed.');
            }
        } catch (Exception $exception) {
            $this->error('Service register failed : ' . $exception->getMessage());
        } finally {
            $client->close();
        }
    }

    /**
     * @param string $name
     * @return array
     */
    protected function config(string $name): array
    {
        return $this->getLaravel()->make('config')->get("micro-service.connections.{$name}");
    }

    /**
     * @return Manager
     */
    protected function client(): Manager
    {
        return $this->getLaravel()->make('client.manager');
    }
}