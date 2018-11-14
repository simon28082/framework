<?php

namespace CrCms\Framework\App\Handlers\Contracts;

use CrCms\Framework\Transporters\Contracts\DataProviderContract;

interface HandlerContract
{
    /**
     * @param DataProviderContract $provider
     * @return mixed
     */
    public function handle(DataProviderContract $provider);
}