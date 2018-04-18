<?php

namespace CrCms\Foundation\App\Http\GraphQL\Traits;

use Illuminate\Support\Str;
use InvalidArgumentException;
use TypeError;

/**
 * Trait FieldTrait
 * @package CrCms\Foundation\App\Http\GraphQL\Traits
 */
trait FieldTrait
{
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @return array
     */
    public function fields(): array
    {
        return collect($this->fields)->mapWithKeys(function ($value, $key) {
            if (is_array($value)) {
                return [$key => $value];
            } elseif (method_exists($this, Str::camel($value) . 'Field')) {
                $result = $this->{Str::camel($value) . 'Field'}();
                if (!is_array($result)) {
                    throw new TypeError("The {$value} must be return array");
                }
                return [$value => $result];
            } else {
                throw new InvalidArgumentException('Params Error');
            }
        })->toArray();
    }
}