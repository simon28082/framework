<?php

namespace CrCms\Foundation\MicroService;

use CrCms\Foundation\Foundation\Contracts\ApplicationContract;
use CrCms\Foundation\MicroService\Contracts\ExceptionHandlerContract;
use CrCms\Foundation\MicroService\Contracts\ServiceContract;
use CrCms\Foundation\MicroService\Exceptions\ExceptionHandler;
use CrCms\Foundation\MicroService\Http\Service;
use CrCms\Foundation\MicroService\Routing\Router;
use CrCms\Foundation\MicroService\Routing\RoutingServiceProvider;
use CrCms\Foundation\Swoole\Server\Contracts\ServerBindApplicationContract;
use CrCms\Foundation\Swoole\Server\Contracts\ServerContract;
use Illuminate\Container\Container;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Support\Collection;
use Illuminate\Log\LogServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\ProviderRepository;
use CrCms\Foundation\Http\Application as Base2Application;
use Illuminate\Support\Str;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Foundation\PackageManifest as BasePackageManifest;
use CrCms\Foundation\MicroService\Contracts\Kernel as KernelContract;

/**
 * Class Application
 * @package CrCms\Foundation\MicroService
 */
class Application extends Base2Application implements ContainerContract, ApplicationContract
{
    public function bindKernel(): void
    {
        $this->singleton(
            KernelContract::class,
            Kernel::class
        );

//        $this->singleton(
//            ExceptionHandlerContract::class,
//            \CrCms\Foundation\MicroService\Http\ExceptionHandler::class
//        );
    }

    public function run(): void
    {
        $kernel = $this->make(KernelContract::class);

        $kernel->bootstrap();

        $service = Factory::service($this,$this['config']->get('ms.default'));
        //这里还有问题，一旦被Service之前有异常或出错，则会报ExceptionHandlerContract没有绑定
        $this->singleton(
            ExceptionHandlerContract::class,
            $service::exceptionHandler()
        );

        $response = $kernel->handle(
            $service
        );

        $response->send();

        $kernel->terminate($service);
    }

    /**
     * Register the core class aliases in the container.
     *
     * @return void
     */
    public function registerCoreContainerAliases()
    {
        foreach ([
                     'app'                  => [\Illuminate\Foundation\Application::class, \Illuminate\Contracts\Container\Container::class, \Illuminate\Contracts\Foundation\Application::class,  \Psr\Container\ContainerInterface::class],
                     'cache'                => [\Illuminate\Cache\CacheManager::class, \Illuminate\Contracts\Cache\Factory::class],
                     'cache.store'          => [\Illuminate\Cache\Repository::class, \Illuminate\Contracts\Cache\Repository::class],
                     'config'               => [\Illuminate\Config\Repository::class, \Illuminate\Contracts\Config\Repository::class],
                     'encrypter'            => [\Illuminate\Encryption\Encrypter::class, \Illuminate\Contracts\Encryption\Encrypter::class],
                     'db'                   => [\Illuminate\Database\DatabaseManager::class],
                     'db.connection'        => [\Illuminate\Database\Connection::class, \Illuminate\Database\ConnectionInterface::class],
                     'events'               => [\Illuminate\Events\Dispatcher::class, \Illuminate\Contracts\Events\Dispatcher::class],
                     'files'                => [\Illuminate\Filesystem\Filesystem::class],
                     'filesystem'           => [\Illuminate\Filesystem\FilesystemManager::class, \Illuminate\Contracts\Filesystem\Factory::class],
                     'filesystem.disk'      => [\Illuminate\Contracts\Filesystem\Filesystem::class],
                     'filesystem.cloud'     => [\Illuminate\Contracts\Filesystem\Cloud::class],
                     'hash'                 => [\Illuminate\Hashing\HashManager::class],
                     'hash.driver'          => [\Illuminate\Contracts\Hashing\Hasher::class],
                     'translator'           => [\Illuminate\Translation\Translator::class, \Illuminate\Contracts\Translation\Translator::class],
                     'log'                  => [\Illuminate\Log\LogManager::class, \Psr\Log\LoggerInterface::class],
                     'mailer'               => [\Illuminate\Mail\Mailer::class, \Illuminate\Contracts\Mail\Mailer::class, \Illuminate\Contracts\Mail\MailQueue::class],
                     'queue'                => [\Illuminate\Queue\QueueManager::class, \Illuminate\Contracts\Queue\Factory::class, \Illuminate\Contracts\Queue\Monitor::class],
                     'queue.connection'     => [\Illuminate\Contracts\Queue\Queue::class],
                     'queue.failer'         => [\Illuminate\Queue\Failed\FailedJobProviderInterface::class],
                     'redis'                => [\Illuminate\Redis\RedisManager::class, \Illuminate\Contracts\Redis\Factory::class],
                     //'request'              => [\Illuminate\Http\Request::class, \Symfony\Component\HttpFoundation\Request::class],
                     'router'               => [Router::class],
                     'validator'            => [\Illuminate\Validation\Factory::class, \Illuminate\Contracts\Validation\Factory::class],
                     'service' => [ServiceContract::class],
                 ] as $key => $aliases) {
            foreach ($aliases as $alias) {
                $this->alias($key, $alias);
            }
        }

        $this->alias('app', static::class);
        $this->alias('app', ApplicationContract::class);
    }


    /**
     * Register all of the base service providers.
     *
     * @return void
     */
    protected function registerBaseServiceProviders()
    {
        $this->register(new EventServiceProvider($this));

        $this->register(new LogServiceProvider($this));

        $this->register(new RoutingServiceProvider($this));
    }
}