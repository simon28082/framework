<?php

namespace CrCms\Foundation\App\Tasks\Contracts;

interface TaskContract
{
    /**
     * @param mixed ...$params
     * @return mixed
     */
    public function handle(...$params);
}