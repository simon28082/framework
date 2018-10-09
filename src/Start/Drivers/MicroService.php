<?php

namespace CrCms\Foundation\Start\Drivers;

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
use CrCms\Foundation\Swoole\Server\ProcessManager;

/**
 * Class MicroService
 * @package CrCms\Foundation\Start\Drivers
 */
class MicroService implements StartContract
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
    {
        $this->app = $app;

        $action = $params[1] ?? 'start';
        array_shift($params);
        $this->output = new SymfonyStyle(
            new ArgvInput($params),
            new ConsoleOutput()
        );

        $this->setServerManager();

        if (in_array($action, $this->allows, true)) {
            try {
                $this->serverManager->{$action}();
                $this->output->success("{$action} successfully");
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
            new ProcessManager($this->config['pid_file'])
        );
    }
}