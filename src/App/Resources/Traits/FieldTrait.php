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
 * Trait FieldTrait
 * @package CrCms\Foundation\App\Resources\Traits
 */
trait FieldTrait
{
    /**
     * @var array
     */
    protected $resourceFields = [];

    /**
     * @var string
     */
    protected $resourceType = 'except';

    /**
     * Set the keys that are supposed to be filtered out.
     *
     * @param array $fields
     * @return FieldTrait
     */
    public function hide(array $fields): self
    {
        $this->resourceFields = $fields;
        $this->resourceType = 'except';
        return $this;
    }

    /**
     * @param array $fields
     * @return FieldTrait
     */
    public function except(array $fields): self
    {
        return $this->hide($fields);
    }

    /**
     * @param array $fields
     * @return FieldTrait
     */
    public function only(array $fields): self
    {
        $this->resourceFields = $fields;
        $this->resourceType = 'only';
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
        return Arr::{$this->resourceType}($array, $this->resourceFields);
    }
}