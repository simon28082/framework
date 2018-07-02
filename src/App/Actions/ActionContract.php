<?php

namespace CrCms\Foundation\App\Actions;

use Illuminate\Support\Collection;

interface ActionContract
{
    /**
     * @param Collection $collects
     * @return mixed
     */
    public function handle(Collection $collects);
}