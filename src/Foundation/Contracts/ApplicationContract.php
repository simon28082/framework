<?php

namespace CrCms\Foundation\Contracts;

/**
 * Interface ApplicationContract
 * @package CrCms\Foundation\Contracts
 */
interface ApplicationContract
{
    /**
     * @return void
     */
    public function bindKernel(): void;

    /**
     * @return void
     */
    public function run(): void;
}