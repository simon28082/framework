<?php

namespace CrCms\Foundation\Console;

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

        $app->make('app.server')->registerConfiguredProviders();

        /*$providers = Collection::make($app->make('config')->get('http.providers'));
        $microServiceProviders = Collection::make($app->make('config')->get('micro-service.providers'));

        (new ProviderRepository($app, new Filesystem, $app->getCachedServicesPath()))
            ->load($providers->merge($microServiceProviders)->toArray());*/
    }
}