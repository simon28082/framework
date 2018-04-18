<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-04-13 21:44
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\App\Resources\Traits;

use CrCms\Foundation\App\Http\Resources\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Trait HideTrait
 * @package CrCms\Foundation\App\Resources\Traits
 */
trait HideTrait
{
    /**
     * @var array
     */
    protected $withoutFields = [];

    /**
     * Set the keys that are supposed to be filtered out.
     *
     * @param array $fields
     * @return $this
     */
    public function hide(array $fields): self
    {
        $this->withoutFields = $fields;
        return $this;
    }

    /**
     * Remove the filtered keys.
     *
     * @param $array
     * @return array
     */
    protected function filterFields(array $array): array
    {
        Arr::forget($array, $this->withoutFields);
        return $array;
    }

    /**
     * Send fields to hide to UsersResource while processing the collection.
     *
     * @param $request
     * @return array
     */
    protected function processCollection(Request $request): array
    {
        return $this->collection->map(function (Resource $resource) use ($request) {
            return $resource->hide($this->withoutFields)->resolve($request);
        })->all();
    }
}