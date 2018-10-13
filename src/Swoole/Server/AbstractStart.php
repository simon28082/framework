<?php

namespace CrCms\Foundation\Swoole\Server;

use CrCms\Foundation\StartContract;
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
 * Class AbstractStart
 * @package CrCms\Foundation\Swoole\Server
 */
class AbstractStart implements StartContract
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

    /**
     * Swoole constructor.
     */
    public function __construct()
    {
        $this->loadConfiguration();
    }

    /**
     * @param string $content
     * @return bool
     */
    protected function log(string $content): bool
    {
        return Async::writeFile(sprintf($this->config['error_log'], Carbon::now()->toDateString()), $content . PHP_EOL, null, FILE_APPEND);
    }

    /**
     * @param Container $app
     * @param array $params
     */
    public function run(Container $app, array $params): void
    {$params = array_values($params);
        $config = require config_path('swoole.php');
        $this->serverManager = $serverManager = new Server\ServerManager(
            $app,
            $config ,
            new \CrCms\Foundation\Swoole\MicroService\Server($app, $config['servers']['micro-service']),
            new ProcessManager($this->config['process_file'])
        );

        $action = $params[1] ?? 'start';
        array_shift($params);
        $this->output = new SymfonyStyle(
            new ArgvInput($params),
            new ConsoleOutput()
        );

//        $this->setServerManager();

        if (in_array($action, $this->allows, true)) {
            try {
                $this->serverManager->{$action}();
                $line = <<<string
********************************************************************
* HTTP | host: 0.0.0.0, port: 80, type: 1, worker: 1, mode: 3
* TCP  | host: 0.0.0.0, port: 8099, type: 1, worker: 1 (Enabled)
********************************************************************
string;

                $this->output->success($line.PHP_EOL."{$action} successfully");
            } catch (Exception $exception) {
                $this->log($exception->getMessage() . PHP_EOL);
//                $this->output->error($exception->getMessage());
                $this->output->error(
                    $exception->getFile().'--'.$exception->getLine().PHP_EOL.
                    $exception->getCode().PHP_EOL.
                    $exception->getMessage().PHP_EOL.
                    $exception->getTraceAsString().PHP_EOL
                );

            }
        } else {
            $this->output->error("Allow only " . implode($this->allows, ' ') . "options");
        }
    }

    /**
     *
     */
    protected function loadConfiguration(): void
    {
        $this->config = require config_path('swoole.php');
    }

    /**
     *
     */
    protected function setServerManager(): void
    {
        $this->serverManager = new Server\ServerManager(
            $this->app,
            $this->config,
            new \CrCms\Foundation\Swoole\Process\ProcessManager($this->config['process_file'])
        );
    }
}