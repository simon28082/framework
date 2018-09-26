<?php

namespace CrCms\Foundation\Http;

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
        parent::bootstrap($app);

        $providers = Collection::make($app->make('config')->get('http.providers'));

        (new ProviderRepository($app, new Filesystem, $app->getCachedServicesPath()))
            ->load($providers->toArray());
    }
}