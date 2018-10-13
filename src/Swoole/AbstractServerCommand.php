<?php

namespace CrCms\Foundation\Swoole;

use Illuminate\Console\Command;
use Exception;

class AbstractServerCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'server {action}';

    /**
     * @return void
     */
    public function handle(): void
    {
//        $action = $this->argument('action');
//dd(get_class($this->laravel));
//        dd($this->arguments());
        (new MicroService())->run($this->laravel,$this->arguments());
//
//        $config = $this->config($name);
//        $client = $this->client();
//        try {
//            $client->connection($name)
//                ->request(
//                    array_get($config, 'register.uri'),
//                    ['payload' => array_except($config['register'], ['uri']), 'method' => 'put']
//                );
//            $response = $client->getResponse();
//            if ($response->getStatusCode() === 200) {
//                $this->info("Service register successful.");
//            } else {
//                $this->error('Service register failed.');
//            }
//        } catch (Exception $exception) {
//            $this->error('Service register failed : ' . $exception->getMessage());
//        } finally {
//            $client->close();
//        }
    }
}