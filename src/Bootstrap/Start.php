<?php

namespace CrCms\Framework;

use CrCms\Framework\Console\Kernel;
use CrCms\Framework\Foundation\Application;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Contracts\Console\Kernel as KernelContract;

/**
 * Class Start
 * @package CrCms\Microservice\Foundation
 */
class Start
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @param array $params
     * @param null|string $basePath
     * @param null|string $mode
     * @return void
     */
    public static function run(array $params = [], ?string $basePath = null): void
    {
        $instance = new static;

        $instance->bootstrap($basePath);

        $instance->getApplication()->runningInConsole() ?
            $instance->runConsole($params) : $instance->runApplication($params);
    }

    /**
     * @return Start
     */
    public static function instance(): Start
    {
        return new static;
    }

    /**
     * @param null|string $basePath
     * @param null|string $mode
     * @return Start
     */
    public function bootstrap(?string $basePath = null): self
    {
        $this->createApplication($basePath);
        $this->baseKernelBinding();

        return $this;
    }

    /**
     * @param null|string $basePath
     * @return Start
     */
    public function createApplication(?string $basePath = null): self
    {
        $basePath ? : $basePath = realpath(__DIR__.'/../../../../../');

        $this->app = new Application($basePath);
        return $this;
    }

    /**
     * @return Application
     */
    public function getApplication(): Application
    {
        return $this->app;
    }

    /**
     * @param array $params
     * @return void
     */
    protected function runConsole(array $params): void
    {
        $kernel = $this->app->make(KernelContract::class);

        $status = $kernel->handle(
            $input = new ArgvInput(array_values($params)),
            new ConsoleOutput
        );

        $kernel->terminate($input, $status);

        exit($status);
    }

    /**
     * @return void
     */
    protected function baseKernelBinding(): void
    {
        $this->app->singleton(
            KernelContract::class,
            Kernel::class
        );

        $this->app->singleton(
            ExceptionHandler::class,
            \Illuminate\Foundation\Exceptions\Handler::class
        );

        $this->app->singleton(\Illuminate\Contracts\Http\Kernel::class,
            \CrCms\Framework\Http\Kernel::class
            );
    }

    /**
     * @param array $params
     * @return void
     */
    protected function runApplication(array $params): void
    {
        $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);
        $response = $kernel->handle(
            $request = \Illuminate\Http\Request::capture()
        );
        $response->send();
        $kernel->terminate($request, $response);
    }
}