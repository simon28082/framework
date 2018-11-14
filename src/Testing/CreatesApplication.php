<?php

namespace CrCms\Framework\Testing;

use CrCms\Framework\Start;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $start = Start::instance();

        $start->bootstrap($this->mode());

        $start->getApp()->make(Kernel::class)->bootstrap();

        Hash::driver('bcrypt')->setRounds(4);

        return $start->getApp();
    }

    /**
     * @return string
     */
    protected function mode(): string
    {
        return isset($this->mode) && !is_null($this->mode) ? $this->mode : 'laravel';
    }
}
