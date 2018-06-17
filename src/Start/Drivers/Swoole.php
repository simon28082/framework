<?php

namespace CrCms\Foundation\Start\Drivers;

use Carbon\Carbon;
use CrCms\Foundation\Swoole\INotify;
use CrCms\Foundation\Swoole\Server;
use CrCms\Foundation\StartContract;
use Illuminate\Contracts\Container\Container;
use Swoole\Async;
use Swoole\Process;
use Exception;
use UnexpectedValueException;
use Illuminate\Contracts\Http\Kernel;

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
     *
     */
    protected function setServerManage(): void
    {
        $this->serverManage = new Server\ServerManage($this->app);
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

        $this->setServerManage();

        if (in_array($action, $this->allows, true)) {
            try {
                $this->serverManage->{$action}();

                echo "{$action} successfully" . PHP_EOL;

            } catch (Exception $exception) {
                //$this->log($exception->getMessage());
                echo $exception->getMessage() . PHP_EOL;
            }
        } else {
            echo "Allow only " . implode($this->allows, ' ') . "options" . PHP_EOL;
        }
    }
}