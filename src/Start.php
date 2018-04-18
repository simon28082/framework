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
     * @param string $drive
     * @param array $params
     * @return void
     */
    public static function run(Container $container, string $drive, array $params): void
    {
        StartFactory::factory($container, $drive)->run($container, $params);
    }
}