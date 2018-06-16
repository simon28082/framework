<?php

namespace CrCms\Foundation\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class DirectoryMakeCommand
 * @package CrCms\Foundation\Console\Commands
 */
class DirectoryMakeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:directory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically create directories and subdirectories';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var array
     */
    protected $modules = ['storage', 'config', 'database', 'modules', 'extensions', 'routes'];

    /**
     * AutoCreateStorageCommand constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->files = $filesystem;
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        $name = $this->argument('name');

        if (in_array($name, $this->modules, true)) {
            call_user_func([$this, 'create' . ucfirst($name)]);
        } else {
            $path = base_path($name);
            if (!$this->files->exists($path)) {
                $this->files->makeDirectory(base_path($name), 0755, true);
            }
        }
    }

    /**
     * @return void
     */
    protected function createDatabase(): void
    {
        $this->autoCreateDirs([
            database_path('factories'),
            database_path('migrations'),
            database_path('seeds'),
        ]);
    }

    /**
     * @return void
     */
    protected function createConfig(): void
    {
        $this->autoCreateDirs(config_path());
    }

    /**
     * @return void
     */
    protected function createStorage(): void
    {
        $this->autoCreateDirs([
            'runCachePath' => storage_path('run-cache'),
            'sessionFilePath' => config('session.files'),
            'cachePath' => config('cache.stores.file.path'),
            'viewPath' => config('view.compiled'),
            'logPath' => storage_path('logs'),
            'appPublicPath' => storage_path('app/public'),
            'testingPath' => storage_path('framework/testing'),
        ]);

        $gitignore = storage_path('.gitignore');
        if (!$this->files->exists($gitignore)) {
            $this->files->put(storage_path('.gitignore'), '*');
        }
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @return void
     */
    protected function createRoutes(): void
    {
        $routePath = base_path('routes');
        if (!$this->files->exists($routePath)) {
            $this->files->makeDirectory($routePath, 0755, true);
        }

        $webFile = base_path('routes/web.php');
        if (!$this->files->exists($webFile)) {
            $this->files->put($webFile, $this->files->get(__DIR__ . '/stubs/web-route.stub'));
        }

        $webFile = base_path('routes/api.php');
        if (!$this->files->exists($webFile)) {
            $this->files->put($webFile, $this->files->get(__DIR__ . '/stubs/api-route.stub'));
        }
    }

    /**
     * @param array $dirs
     * @return void
     */
    protected function autoCreateDirs(array $dirs): void
    {
        foreach ($dirs as $dir) {
            if (!$this->files->exists($dir)) {
                $this->files->makeDirectory($dir, 0755, true);
            }
        }
    }

    /**
     * @return void
     */
    protected function createModules(): void
    {
        return $this->autoCreateDirs([
            base_path('modules'),
        ]);
    }

    /**
     * @return void
     */
    protected function createExtensions(): void
    {
        return $this->autoCreateDirs([
            base_path('extensions'),
        ]);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the directory'],
        ];
    }
}