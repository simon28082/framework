<?php

namespace CrCms\Foundation;

use Illuminate\Http\Request;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

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
     * @var string
     */
    protected $mode;

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
    public static function run(array $params = [], ?string $basePath = null): void
    {
        static::instance()->handle($params);
    }

    /**
     * @return Start
     */
    public static function instance(): self
    {
        return new static;
    }

    /**
     * @param array $params
     * @param null|string $basePath
     * @return void
     */
    public function handle(array $params, ?string $basePath = null): void
    {
        $this->bootstrap($params['mode'] ?? $params[1] ?? null, $basePath);

        $this->app->runningInConsole() ? $this->runConsole($params) : $this->runWeb($params);
    }

    /**
     * @param null|string $mode
     * @param null|string $basePath
     * @return Start
     */
    public function bootstrap(?string $mode = null, ?string $basePath = null): self
    {
        $this->mode = $this->mode($mode);

        $this->app = $this->app($basePath);

        $this->loadKernel();

        return $this;
    }

    /**
     * @return Application
     */
    public function getApp(): Application
    {
        return $this->app;
    }

    /**
     * @param null|string $basePath
     * @return Application
     */
    protected function app(?string $basePath = null): Application
    {
        $serverApplicationName = $this->parseServerApplication();

        return new \CrCms\Foundation\Application(
            $basePath ? $basePath : realpath(__DIR__ . '/../../../../'),
            new $serverApplicationName
        );
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mode(?string $mode = null): string
    {
        $envMode = getenv('CRCMS_MODE');
        if ($envMode !== false) {
            $mode = $envMode;
        }

        $mode = strtolower($mode);

        if (!array_key_exists($mode, static::DRIVERS)) {
            $mode = 'laravel';
        }

        putenv("CRCMS_MODE={$mode}");

        return $mode;
    }

    /**
     * 过滤CLI运行模式参数
     *
     * @param string $mode
     * @param array $params
     * @return array
     */
    protected function consoleParams(array $params): array
    {
        //如果指定了运行模式则判断后直接删除模式，否则会影响命令行
        isset($params[1]) && $this->mode === $params[1] ? array_forget($params, 1) : null;
        array_forget($params, ['mode']);

        return $params;
    }

    /**
     * @return string
     */
    protected function parseServerApplication(): string
    {
        return static::DRIVERS[$this->mode];
    }

    /**
     * @return void
     */
    public function loadKernel()
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
        $params = $this->consoleParams($params);

        $kernel = $this->app->make(\Illuminate\Contracts\Console\Kernel::class);

        $status = $kernel->handle(
            $input = new ArgvInput(array_values($params)),
            new ConsoleOutput
        );

        $kernel->terminate($input, $status);

        exit($status);
    }
}