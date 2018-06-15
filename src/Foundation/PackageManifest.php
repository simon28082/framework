<?php

namespace CrCms\Foundation\Foundation;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\PackageManifest as BasePackageManifest;

class PackageManifest extends BasePackageManifest
{
    /**
     * Write the given manifest array to disk.
     *
     * @param  array  $manifest
     * @return void
     * @throws \Exception
     */
    protected function write(array $manifest)
    {
        $manifestPathDir = dirname($this->manifestPath);

        if (!is_dir($manifestPathDir)) {
            $this->files->makeDirectory($manifestPathDir,0755,true);
        }

        if (! is_writable($manifestPathDir)) {
            throw new Exception('The '.dirname($this->manifestPath).' directory must be present and writable.');
        }

        $this->files->put(
            $this->manifestPath, '<?php return '.var_export($manifest, true).';'
        );
    }
}
