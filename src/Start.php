<?php

namespace CrCms\Foundation;

use Illuminate\Contracts\Container\Container;

/**
 * Class Start
 * @package CrCms\Foundation
 */
class Start
{
    /**
     * @param Container $container
     * @param StartContract $startContract
     * @param array $params
     * @return void
     */
    public static function run(Container $container, array $params): void
    {
        StartFactory::factory($container,
            $params[0] ?? \CrCms\Foundation\StartFactory::TYPE_LARAVEL
        )->run($container, $params);
    }
}