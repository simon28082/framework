<?php

namespace CrCms\Framework\Foundation\Bootstrap;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Bootstrap\RegisterProviders as BaseRegisterProviders;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\ProviderRepository;
use Illuminate\Support\Collection;

class RegisterProviders extends BaseRegisterProviders
{
    /**
     * @param Application $app
     */
    public function bootstrap(Application $app)
    {
        //parent::bootstrap($app);
        $app->getServerApplication()->registerConfiguredProviders();
    }
}