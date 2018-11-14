<?php

namespace CrCms\Framework\Bootstrap;

use Illuminate\Foundation\PackageManifest;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Facade;
use Illuminate\Contracts\Foundation\Application;

/**
 * Class RegisterFacades
 * @package CrCms\Framework\Bootstrap
 */
class RegisterFacades
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        Facade::clearResolvedInstances();

        Facade::setFacadeApplication($app);

        AliasLoader::getInstance(array_merge(
            $app->make('config')->get('mount.aliases', []),
            $app->make(PackageManifest::class)->aliases()
        ))->register();
    }
}