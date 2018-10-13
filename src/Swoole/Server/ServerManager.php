<?php

namespace CrCms\Foundation\Swoole\Server;

use CrCms\Foundation\Application;
use CrCms\Foundation\StartContract;
use CrCms\Foundation\Swoole\Server\Contracts\ServerContract;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\Container;
use CrCms\Foundation\MicroService\Server\Kernel as HttpKernelContract;
use Illuminate\Http\Request;
use Carbon\Carbon;
use CrCms\Foundation\Swoole\Server;
use Swoole\Async;
use Exception;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use function CrCms\Foundation\App\Helpers\array_merge_recursive_distinct;
use function CrCms\Foundation\App\Helpers\framework_config_path;
use CrCms\Foundation\Swoole\Process\ProcessManager;

/**
 * Class ServerManager
 * @package CrCms\Foundation\Swoole\Server
 */
class ServerManager implements Server\Contracts\ServerStartContract, Server\Contracts\ServerActionContract
{
    /**
     * @var array
     */
    protected $allows = ['start', 'stop', 'restart', 'reload'];

    /**
     * @var Server\ServerManager
     */
    protected $serverManager;

    /**
     * @var Container
     */
    protected $app;

    /**
     * @var SymfonyStyle
     */
    protected $output;

    /**
     * @var array
     */
    protected $config;

    protected $command;

    protected $server;

    protected $process;

    /**
     * Swoole constructor.
     */
//    public function __construct(Command $command, ServerContract $server, ProcessManager $process)
//    {
//        $this->command = $command;
//        $this->server = $server;
//        $this->process = $process;
//    }

    /**
     * @param string $content
     * @return bool
     */
    protected function log(string $content): bool
    {
        return Async::writeFile(sprintf($this->config['error_log'], Carbon::now()->toDateString()), $content . PHP_EOL, null, FILE_APPEND);
    }

    public function run(Command $command, ServerContract $server, ProcessManager $process): void
    {
        $this->command = $command;
        $this->server = $server;
        $this->process = $process;

        $action = $command->argument('action');

        if (in_array($action, $this->allows, true)) {
            try {
                $this->{$action}();
                $line = <<<string
********************************************************************
* HTTP | host: 0.0.0.0, port: 80, type: 1, worker: 1, mode: 3
* TCP  | host: 0.0.0.0, port: 8099, type: 1, worker: 1 (Enabled)
********************************************************************
string;

                $command->getOutput()->block($line . PHP_EOL . "{$action} successfully");
            } catch (Exception $exception) {
//                $this->log($exception->getMessage() . PHP_EOL);
                $command->getOutput()->error($exception->getMessage());
                $command->getOutput()->error(
                    $exception->getFile() . '--' . $exception->getLine() . PHP_EOL .
                    $exception->getCode() . PHP_EOL .
                    $exception->getMessage() . PHP_EOL .
                    $exception->getTraceAsString() . PHP_EOL
                );

            }
        } else {
            $command->getOutput()->error("Allow only " . implode($this->allows, ' ') . "options");
        }
    }

    public function start(): bool
    {
        $this->process->start(
            new Server\Processes\ServerProcess(
                $this->server
            ),
            $this->command->argument('command')
        );
    }

    public function stop(): bool
    {
        dump($this->process->kill($this->command->argument('command')));

        return true;
    }

    public function restart(): bool
    {
        // TODO: Implement restart() method.
    }


}