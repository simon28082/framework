<?php

namespace CrCms\Framework\App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    /**
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * @var array
     */
    protected $guarded = [];
}