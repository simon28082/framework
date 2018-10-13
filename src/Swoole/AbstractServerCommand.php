<?php

namespace CrCms\Foundation\Swoole;

use CrCms\Foundation\Swoole\Process\ProcessManager;
use CrCms\Foundation\Swoole\Server\Contracts\ServerContract;
use CrCms\Foundation\Swoole\Server\ServerManager;
use Illuminate\Console\Command;
use Exception;

abstract class AbstractServerCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'server:%s {action : start or stop or restart}';

    /**
     * @var string
     */
    protected $server;

    /**
     * AbstractServerCommand constructor.
     */
    public function __construct()
    {
        $this->signature = sprintf($this->signature, $this->server);
        parent::__construct();
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        (new ServerManager)->run(
            $this,
            $this->server(),
            new ProcessManager(config('swoole.process_file'))
        );
    }

    /**
     * @return ServerContract
     */
    abstract public function server(): ServerContract;
}