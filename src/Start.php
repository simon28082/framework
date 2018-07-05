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
     * @param string $driver
     * @param array $params
     * @return void
     */
    public static function run(Container $container, string $driver, array $params): void
    {
        StartFactory::factory($container, $driver)->run($container, $params);
    }
}