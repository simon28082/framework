<?php

namespace CrCms\Foundation;

use Illuminate\Http\Request;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;

/**
 * Class Start
 * @package CrCms\Foundation
 */
class Start
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var array
     */
    const DRIVERS = [
        'laravel' => \CrCms\Foundation\Laravel\Application::class,
        'ms' => \CrCms\Foundation\MicroService\Application::class,
    ];

    /**
     * @param array $params
     * @param null|string $basePath
     * @return void
     */
    public static function run(array $params, ?string $basePath = null): void
    {
        (new static())->handle($params);
    }

    /**
     * @param array $params
     * @param null|string $basePath
     * @return void
     */
    public function handle(array $params, ?string $basePath = null)
    {
        $mode = $this->mode($params);

        $params = $this->filterParams($mode, $params);

        $this->app = $this->app(
            $this->parseServerApplication($mode),
            $basePath
        );

        $this->loadKernel();

        $this->app->runningInConsole() ? $this->runConsole($params) : $this->runWeb($params);
    }

    /**
     * @param string $serverApplicationName
     * @param null|string $basePath
     * @return Application
     */
    protected function app(string $serverApplicationName, ?string $basePath = null): Application
    {
        return new \CrCms\Foundation\Application(
            $basePath ? $basePath : realpath(__DIR__ . '/../../../../'),
            new $serverApplicationName
        );
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mode(array $params): string
    {
        $mode = getenv('CRCMS_MODE');
        if ($mode === false) {
            $mode = $params[1] ?? null;
        }

        $mode = strtolower($mode);

        if (!array_key_exists($mode, static::DRIVERS)) {
            $mode = 'laravel';
        }

        putenv("CRCMS_MODE={$mode}");

        return $mode;
    }

    /**
     * 过滤运行模式参数
     *
     * @param string $mode
     * @param array $params
     * @return array
     */
    protected function filterParams(string $mode, array $params): array
    {
        //如果指定了运行模式则判断后直接删除模式，否则会影响命令行
        isset($params[1]) && $mode === $params[1] ? array_forget($params, 1) : null;

        return $params;
    }

    /**
     * @param string $mode
     * @return string
     */
    protected function parseServerApplication(string $mode): string
    {
        return static::DRIVERS[$mode];
    }

    /**
     * @return void
     */
    protected function loadKernel()
    {
        $this->app->singleton(
            \Illuminate\Contracts\Console\Kernel::class,
            \CrCms\Foundation\Console\Kernel::class
        );

        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \CrCms\Foundation\App\Exceptions\Handler::class
        );

        $this->app->getServerApplication()->loadKernel();
    }

    /**
     * @param array $params
     * @return void
     */
    protected function runWeb(array $params)
    {
        $kernel = $this->app->make($this->app->getServerApplication()->kernel());

        $response = $kernel->handle(
            $request = Request::capture()
        );

        $response->send();

        $kernel->terminate($request, $response);
    }

    /**
     * @param array $params
     * @return void
     */
    protected function runConsole(array $params)
    {
        $kernel = $this->app->make(\Illuminate\Contracts\Console\Kernel::class);

        $status = $kernel->handle(
            $input = new ArgvInput(array_values($params)),
            new ConsoleOutput
        );

        $kernel->terminate($input, $status);

        exit($status);
    }
}