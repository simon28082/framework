<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 22:01
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;

trait CreatesApplication
{
    public function createApplication()
    {
        $app = require __DIR__.'/../src/Bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        Hash::driver('bcrypt')->setRounds(4);

        return $app;
    }
}