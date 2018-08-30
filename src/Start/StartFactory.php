<?php

namespace CrCms\Foundation;

use CrCms\Foundation\Start\Drivers\Artisan;
use CrCms\Foundation\Start\Drivers\Swoole;
use CrCms\Foundation\Start\Drivers\Laravel;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

/**
 * Class Factory
 * @package CrCms\Foundation
 */
class StartFactory
{
    /**
     *
     */
    const TYPE_LARAVEL = 'bin/laravel';

    /**
     *
     */
    const TYPE_SWOOLE = 'bin/swoole';

    /**
     *
     */
    const TYPE_ARTISAN = 'bin/artisan';

    /**
     * @param Container $app
     * @param string $type
     * @return StartContract
     */
    public static function factory(Container $app, string $type = self::TYPE_LARAVEL): StartContract
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
            self::TYPE_LARAVEL => Laravel::class,
            self::TYPE_SWOOLE => Swoole::class,
            self::TYPE_ARTISAN => Artisan::class,
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
        } elseif (stripos($type, self::TYPE_SWOOLE) !== false) {
            return self::TYPE_SWOOLE;
        } elseif (stripos($type, self::TYPE_ARTISAN) !== false) {
            return self::TYPE_ARTISAN;
        } else {
            return self::TYPE_LARAVEL;
            //throw new InvalidArgumentException('Run driver not found');
        }
    }
}