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
        'micro-service' => \CrCms\Foundation\MicroService\Application::class,
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
        $this->app = $this->app(
            $this->parseServerApplication($params[1] ?? ''),
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
     * @param string $driver
     * @return string
     */
    protected function parseServerApplication(string $driver): string
    {
        $driver = strpos($driver, ':') ? explode(':', $driver)[1] : $driver;

        if (!array_key_exists($driver, static::DRIVERS)) {
            $driver = 'laravel';
        }

        return static::DRIVERS[$driver];
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
        $kernel = $this->app->make(HttpKernelContract::class);

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