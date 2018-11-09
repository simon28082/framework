<?php

namespace CrCms\Foundation\MicroService\Contracts;

use CrCms\Foundation\Foundation\Contracts\ApplicationContract;

/**
 * Interface Kernel
 * @package CrCms\Foundation\Rpc\Server\Contracts
 */
interface Kernel
{
    /**
     * @return ResponseContract
     */
    public function bootstrap(): void ;

    /**
     * @param ServiceContract $service
     * @return ResponseContract
     */
    public function handle(ServiceContract $service): ResponseContract;

    /**
     * @param ServiceContract $service
     * @return mixed
     */
    public function terminate(ServiceContract $service);

    /**
     * @return ApplicationContract
     */
    public function getApplication(): ApplicationContract;
}