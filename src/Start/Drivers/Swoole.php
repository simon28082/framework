<?php

namespace CrCms\Foundation\Start\Drivers;

use Carbon\Carbon;
use CrCms\Foundation\Swoole\INotify;
use CrCms\Foundation\Swoole\Server;
use CrCms\Foundation\StartContract;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\Container;
use Swoole\Async;
use Swoole\Process;
use Exception;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use UnexpectedValueException;
use Illuminate\Contracts\Http\Kernel;
use function CrCms\Foundation\App\Helpers\array_merge_recursive_distinct;
use function CrCms\Foundation\App\Helpers\framework_config_path;
use CrCms\Foundation\Swoole\Server\ProcessManage;

/**
 * Class Swoole
 * @package CrCms\Foundation\Start\Drivers
 */
class Swoole implements StartContract
{
    /**
     * @var array
     */
    protected $allows = ['start', 'stop', 'restart', 'reload'];

    /**
     * @var Server\ServerManage
     */
    protected $serverManage;

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

        $this->setServerManage();

        if (in_array($action, $this->allows, true)) {
            try {
                $this->serverManage->{$action}();
                $this->output->success("{$action} successfully");
            } catch (Exception $exception) {
                $this->log($exception->getMessage() . PHP_EOL);
                $this->output->error($exception->getMessage());
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
        $config = require framework_config_path('swoole.php');

        $customConfigPath = config_path('swoole.php');
        if (file_exists($customConfigPath) && is_file(file_exists($customConfigPath))) {
            $config = array_merge_recursive_distinct($config, require $customConfigPath);
        }

        $this->config = $config;
    }

    /**
     *
     */
    protected function setServerManage(): void
    {
        $this->serverManage = new Server\ServerManage(
            $this->app,
            $this->config,
            new ProcessManage($this->config['pid_file'])
        );
    }
}