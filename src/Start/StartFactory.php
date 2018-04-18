<?php

namespace CrCms\Foundation;

use CrCms\Foundation\Start\Drivers\Artisan;
use CrCms\Foundation\Start\Drivers\Swoole;
use CrCms\Foundation\Start\Drivers\Laravel;
use Illuminate\Contracts\Container\Container;

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
        $app->singleton(
            StartContract::class,
            static::drivers()[$type]
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
}