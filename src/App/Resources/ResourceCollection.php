<?php

namespace CrCms\Foundation\App\Http\Resources;

use CrCms\Foundation\App\Resources\Traits\HideTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection as BaseResourceCollection;

/**
 * Class ResourceCollection
 * @package CrCms\Foundation\Http\Resources
 */
class ResourceCollection extends BaseResourceCollection
{
    use HideTrait;

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->processCollection($request);
//        return $this->collection->map->toArray($request)->all();
    }
}