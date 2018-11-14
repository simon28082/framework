<?php

namespace CrCms\Framework\Foundation\Contracts;

/**
 * Interface ApplicationContract
 * @package CrCms\Framework\Contracts
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