<?php

namespace CrCms\Foundation\App\Actions;

use Illuminate\Support\Collection;

interface ActionContract
{
    /**
     * @param array $data
     * @return mixed
     */
    public function handle(array $data = []);
}