<?php

namespace CrCms\Foundation\App\Actions;

use Illuminate\Support\Collection;

interface Contract
{
    /**
     * @param Collection $collects
     * @return mixed
     */
    public function handle(Collection $collects);
}