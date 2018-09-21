<?php

namespace CrCms\Foundation\Rpc\Commands;

use CrCms\Foundation\Client\Manager;
use Illuminate\Console\Command;
use Exception;

/**
 * Class ServiceRegisterCommand
 * @package CrCms\Foundation\Rpc\Commands
 */
class ServiceRegisterCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'rpc:register';

    /**
     * @return void
     */
    public function handle(): void
    {
        $config = $this->config('consul');
        $client = $this->client();
        try {
            $client->connection('consul')
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
        return $this->getLaravel()->make('config')->get("rpc.connections.{$name}");
    }

    /**
     * @return Manager
     */
    protected function client(): Manager
    {
        return $this->getLaravel()->make('client.manager');
    }
}