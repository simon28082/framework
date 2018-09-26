<?php

namespace CrCms\Foundation;

use Illuminate\Contracts\Container\Container;

/**
 * Interface StartContract
 * @package CrCms\Foundation
 */
interface StartContract
{
    /**
     * @param Container $app
     * @return void
     */
    //public function register(Container $app): void;

    /**
     * @param Container $app
     * @param array $params
     * @return void
     */
    public function run(Container $app, array $params): void;
}