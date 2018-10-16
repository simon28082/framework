<?php

namespace CrCms\Foundation\Swoole\Server;

use CrCms\Foundation\Swoole\Server\Contracts\ServerContract;
use Illuminate\Console\Command;
use CrCms\Foundation\Swoole\Server;
use Exception;
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
    protected $allows = ['start', 'stop', 'restart'];//, 'reload'

    /**
     * @var Command
     */
    protected $command;

    /**
     * @var ServerContract
     */
    protected $server;

    /**
     * @var ProcessManager
     */
    protected $process;

    /**
     * @param Command $command
     * @param ServerContract $server
     * @param ProcessManager $process
     */
    public function run(Command $command, ServerContract $server, ProcessManager $process): void
    {
        $this->command = $command;
        $this->server = $server;
        $this->process = $process;

        $action = $command->argument('action');

        if (in_array($action, $this->allows, true)) {
            try {
                if ($this->{$action}()) {
                    $command->getOutput()->success("{$action} successfully");
                } else {
                    $command->getOutput()->success("{$action} failed");
                }
            } catch (Exception $exception) {
                $command->getOutput()->error($exception->getMessage());
                $command->getOutput()->block(
                    "File:{$exception->getFile()} Line:{$exception->getLine()}" . PHP_EOL .
                    "Message:" . $exception->getMessage() . PHP_EOL .
                    "Code:" . $exception->getCode() . PHP_EOL .
                    "Trace:" . PHP_EOL .
                    $exception->getTraceAsString() . PHP_EOL
                );
            }
        } else {
            $command->getOutput()->error("Allow only " . implode($this->allows, ' ') . "options");
        }
    }

    /**
     * @return bool
     */
    public function start(): bool
    {
        if ($this->process->exists($this->command->argument('command'))) {
            return true;
        }

        return $this->process->start(
            new Server\Processes\ServerProcess(
                $this->server, true
            ),
            $this->command->argument('command')
        );
    }

    /**
     * @return bool
     */
    public function stop(): bool
    {
        if (!$this->process->exists($this->command->argument('command'))) {
            return true;
        }

        return $this->process->kill($this->command->argument('command'));
    }

    /**
     * @return bool
     */
    public function restart(): bool
    {
        $this->stop();
        sleep(2);
        return $this->start();
    }
}