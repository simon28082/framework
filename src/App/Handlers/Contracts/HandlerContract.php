<?php

namespace CrCms\Foundation\App\Handlers\Contracts;

use CrCms\Foundation\Transporters\Contracts\DataProviderContract;

interface HandlerContract
{
    /**
     * @param DataProviderContract $provider
     * @return mixed
     */
    public function handle(DataProviderContract $provider);
}