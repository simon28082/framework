<?php

namespace CrCms\Foundation;

use CrCms\Foundation\Start\Drivers\Artisan;
use CrCms\Foundation\Start\Drivers\HTTP;
use CrCms\Foundation\Start\Drivers\Laravel;
use CrCms\Foundation\Start\Drivers\Rpc;
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
    const TYPE_RPC = 'RPC';

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
            self::TYPE_HTTP => HTTP::class,
            self::TYPE_ARTISAN => Artisan::class,
            self::TYPE_RPC => Rpc::class,
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
        } elseif (stripos($type, self::TYPE_RPC) !== false) {
            return self::TYPE_RPC;
        }

        throw new InvalidArgumentException('Run driver not found');
    }
}