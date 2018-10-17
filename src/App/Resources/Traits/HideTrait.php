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
    protected $fields = [];

    /**
     * @var string
     */
    protected $type;

    /**
     * Set the keys that are supposed to be filtered out.
     *
     * @param array $fields
     * @return $this
     */
    public function hide(array $fields): self
    {
        $this->fields = $fields;
        $this->type = 'hide';
        return $this;
    }

    /**
     * @param array $fields
     * @return HideTrait
     */
    public function only(array $fields): self
    {
        $this->fields = $fields;
        $this->type = 'only';
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
        return Arr::{$this->type}($array, $this->fields);
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
            return $resource->{$this->type}($this->fields)->resolve($request);
        })->all();
    }
}