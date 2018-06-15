<?php

namespace CrCms\Foundation\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;

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
    protected $modules = ['storage', 'config', 'database'];

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
     *
     */
    public function handle(): void
    {
        $name = $this->argument('name');

        if (in_array($name, $this->modules, true)) {
            $modules = $this->dirs($name);
            foreach ($modules as $dir) {
                if (!$this->files->exists($dir)) {
                    $this->files->makeDirectory($dir, 0755, true);
                }
            }
        } else {
            $path = base_path($name);
            if (!$this->files->exists($path)) {
                $this->files->makeDirectory(base_path($name), 0755, true);
            }
        }

        $gitignore = storage_path('.gitignore');
        if (!$this->files->exists($gitignore)) {
            $this->files->put(storage_path('.gitignore'), '*');
        }
    }

    /**
     * @return array
     */
    protected function dirs(string $module = ''): array
    {
        $modules = [
            'storage' => $this->storageDirs(),
            'config' => $this->configDirs(),
            'database' => $this->databaseDirs(),
        ];

        return empty($module) ? $modules : $modules[$module] ?? $modules;
    }

    /**
     * @return array
     */
    protected function storageDirs(): array
    {
        return [
            'runCachePath' => storage_path('run-cache'),
            'sessionFilePath' => config('session.files'),
            'cachePath' => config('cache.stores.file.path'),
            'viewPath' => config('view.compiled'),
            'logPath' => storage_path('logs'),
            'appPublicPath' => storage_path('app/public'),
            'testingPath' => storage_path('framework/testing'),
        ];
    }

    /**
     * @return array
     */
    protected function configDirs(): array
    {
        return [config_path()];
    }

    /**
     * @return array
     */
    protected function databaseDirs(): array
    {
        return [
            database_path('factories'),
            database_path('migrations'),
            database_path('seeds'),
        ];
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