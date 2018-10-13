<?php

namespace CrCms\Foundation\MicroService\Server\Commands;

use CrCms\Foundation\Client\Manager;
use CrCms\Foundation\MicroService\Server\Server;
use CrCms\Foundation\Start\Drivers\MicroService;
use CrCms\Foundation\Swoole\Process\ProcessManager;
use CrCms\Foundation\Swoole\Server\ServerManager;
use Illuminate\Console\Command;
use Exception;

class ServerCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'server:micro-service {action : start or stop or restart}';


    /**
     * @return void
     */
    public function handle(): void
    {
//        $action = $this->argument('action');
//dd(get_class($this->laravel));
//        dd($this->arguments());

//        $this->serverManager = $serverManager = new Server\ServerManager(
//            $app,
//            $config ,
//            new \CrCms\Foundation\Swoole\MicroService\Server($app, $config['servers']['micro-service']),
//
//        );

        (new ServerManager)->run(
            $this,
            new \CrCms\Foundation\Swoole\MicroService\Server(
                $this->getLaravel(),
                config('swoole.servers.micro-service')
            ),
            new ProcessManager(config('swoole.process_file'))
        );
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