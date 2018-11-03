<?php

namespace CrCms\Foundation\App\Http\Resources;

use CrCms\Foundation\App\Resources\Concerns\FieldConcern;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection as BaseResourceCollection;

/**
 * Class ResourceCollection
 * @package CrCms\Foundation\Http\Resources
 */
class ResourceCollection extends BaseResourceCollection
{
    use FieldConcern;

    /**
     * ResourceCollection constructor.
     * @param $resource
     * @param string $collect
     */
    public function __construct($resource, ?string $collect = null)
    {
        $this->collects = $collect ? $collect : get_called_class();
        parent::__construct($resource);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (Resource $resource) use ($request) {
            return $resource->{$this->resourceType}($this->resourceFields)->resolve($request);
        })->all();
    }
}