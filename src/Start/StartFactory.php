<?php

namespace CrCms\Foundation;

use CrCms\Foundation\Start\Drivers\Artisan;
use CrCms\Foundation\Start\Drivers\HTTP;
use CrCms\Foundation\Start\Drivers\Laravel;
use CrCms\Foundation\Start\Drivers\Test;
use CrCms\Foundation\Start\Drivers\MicroService;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

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
    public static function factory(Container $app, string $type = self::TYPE_HTTP): StartContract
    {
        $driver = static::driver($type);

        $app->singleton(
            StartContract::class,
            static::drivers()[$driver]
        );

        return $app->make(StartContract::class);
    }

    /**
     * @return array
     */
    protected static function drivers(): array
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