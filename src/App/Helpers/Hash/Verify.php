<?php

namespace CrCms\Foundation\App\Helpers\Hash;

use CrCms\Foundation\App\Helpers\Hash\Contracts\HashVerify;
use CrCms\Foundation\App\Helpers\Hash\Traits\VerifyTrait;

class Verify implements HashVerify
{
    use VerifyTrait;

    /**
     * @param array $values
     * @return string
     */
    protected function combination(array $values): string
    {
        return implode(',', $values) . config('app.key');
    }
}