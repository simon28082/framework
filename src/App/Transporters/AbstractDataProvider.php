<?php

/**
 * @author simon <simon@crcms.cn>
 * @datetime 2018-08-12 11:26
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Framework\Transporters;

use CrCms\Framework\Transporters\Concerns\InteractsWithData;
use CrCms\Framework\Transporters\Contracts\DataProviderContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use DomainException;
use Illuminate\Support\Arr;
use ArrayAccess;

/**
 * Class AbstractDataProvider
 * @package CrCms\Framework\Transporters
 */
abstract class AbstractDataProvider implements DataProviderContract, ArrayAccess
{
    use InteractsWithData;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * AbstractDataProvider constructor.
     * @param $object
     */
    public function __construct($object)
    {
        $this->data = $this->resolveData($object);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return Arr::has($this->all(), $key);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return data_get(
            $this->data, $key, $default
        );
    }

    /**
     * @param array $keys
     * @return array
     */
    public function only(array $keys): array
    {
        $results = [];

        $placeholder = new \stdClass;

        foreach ($keys as $key) {
            $value = data_get($this->all(), $key, $placeholder);

            if ($value !== $placeholder) {
                Arr::set($results, $key, $value);
            }
        }

        return $results;
    }

    /**
     * @param array $keys
     * @return array
     */
    public function except(array $keys): array
    {
        $results = $this->all();

        Arr::forget($results, $keys);

        return $results;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * @param string $key
     */
    public function remove(string $key): void
    {
        unset($this->data[$key]);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if ((bool)$value = $this->has($name)) {
            return $value;
        }

        throw new DomainException("The param[{$name}] not found");
    }

    /**
     * @param $object
     * @return array
     */
    protected function resolveData($object)
    {
        if ($object instanceof Request) {
            return array_merge($object->route()->parameters(), $object->all());
        } elseif ($object instanceof Arrayable) {
            return $object->toArray();
        } elseif (is_array($object)) {
            return $object;
        } elseif (is_object($object)) {
            return get_object_vars($object);
        } else {
            throw new DomainException("The object range error");
        }
    }
}