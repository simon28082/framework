<?php

namespace CrCms\Foundation;

use CrCms\Foundation\Start\Drivers\Artisan;
use CrCms\Foundation\Start\Drivers\Http;
use CrCms\Foundation\Start\Drivers\Laravel;
use CrCms\Foundation\Start\Drivers\MicroService;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class Factory
 * @package CrCms\Foundation
 */
class StartFactory
{
    /**
     * @var string
     */
    const TYPE_LARAVEL = 'LARAVEL';

    /**
     * @var string
     */
    const TYPE_HTTP = 'HTTP';

    /**
     * @var string
     */
    const TYPE_ARTISAN = 'ARTISAN';

    /**
     * @var string
     */
    const TYPE_MICRO_SERVICE = 'MICRO_SERVICE';

    /**
     * @param Container $app
     * @param string $type
     * @return StartContract
     */
    public static function factory(Application $app, array $params): StartContract
    {
        $driver = static::parseDriver($params);

        static::loadKernel($app, $driver);

        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

        $status = $kernel->handle(
            $input = new ArgvInput(array_values($params)),
            new ConsoleOutput
        );

        /*
        |--------------------------------------------------------------------------
        | Shutdown The Application
        |--------------------------------------------------------------------------
        |
        | Once Artisan has finished running, we will fire off the shutdown events
        | so that any final work may be done by the application before we shut
        | down the process. This is the last thing to happen to the request.
        |
        */

        $kernel->terminate($input, $status);

        exit($status);

        /*$app->singleton(
            StartContract::class,
            static::drivers()[$driver]
        );

        return $app->make(StartContract::class);*/
    }

    protected static function parseDriver(array $params)
    {
        return 'micro-service';
    }

    protected static function loadKernel(Application $app, string $driver)
    {
//        $app->singleton(
//            \Illuminate\Contracts\Http\Kernel::class,
//            \CrCms\Foundation\Http\Kernel::class
//        );
        $class = 'CrCms\\Foundation\\'.Str::ucfirst(Str::camel($driver)).'\\Application';
        $application = new $class($app);

        $app->initApplication($application);


        $app->singleton(
            \Illuminate\Contracts\Console\Kernel::class,
            \CrCms\Foundation\Artisan\Kernel::class
        );

        $app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \CrCms\Foundation\App\Exceptions\Handler::class
        );



        $app->loadServerApplication($application)->loadKernel();
    }

    /**
     * @return array
     */
    protected static function drivers2(): array
    {
        return [
//            self::TYPE_LARAVEL => Laravel::class,
            self::TYPE_HTTP => Laravel::class,
            self::TYPE_ARTISAN => Artisan::class,
            self::TYPE_MICRO_SERVICE => MicroService::class,
        ];
    }

    /**
     * @param string $type
     * @return string
     */
    protected static function driver(string $type): string
    {
        if (stripos($type, self::TYPE_LARAVEL) !== false) {
            return self::TYPE_LARAVEL;
        } elseif (stripos($type, self::TYPE_HTTP) !== false) {
            return self::TYPE_HTTP;
        } elseif (stripos($type, self::TYPE_ARTISAN) !== false) {
            return self::TYPE_ARTISAN;
        } elseif (stripos($type, self::TYPE_MICRO_SERVICE) !== false) {
            return self::TYPE_MICRO_SERVICE;
        }

        throw new InvalidArgumentException('Run driver not found');
    }
}