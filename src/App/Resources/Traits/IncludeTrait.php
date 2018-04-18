<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-04-13 21:08
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\App\Resources\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Trait IncludeTrait
 * @package CrCms\Foundation\App\Resources
 */
trait IncludeTrait
{
    /**
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * @var string
     */
    protected $includeRequestKey = 'include';

    /**
     * @param array $includes
     * @return self
     */
    public function setIncludes(array $includes): self
    {
        $this->defaultIncludes = $includes;

        return $this;
    }

    /**
     * @param string $include
     * @return self
     */
    public function addInclude(string $include): self
    {
        if (!in_array($include, $this->defaultIncludes, true)) {
            $this->defaultIncludes[] = $include;
        }
        return $this;
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function parseIncludes(Request $request)
    {
        return $this->execIncludes(
            $this->parseParams($request), $request
        );
    }

    /**
     * @param $request
     * @return array
     */
    protected function parseParams($request): array
    {
        return array_merge(explode(',', $request->input($this->includeRequestKey)), $this->defaultIncludes);
    }

    /**
     * @param array $includes
     * @param Request $request
     * @return array
     */
    protected function execIncludes(array $includes, Request $request): array
    {
        return collect($includes)->map(function ($include) {
            return [
                'key' => $include,
                'method' => Str::camel("include-{$include}"),
            ];
        })->filter(function ($include) {
            return method_exists($this, $include['method']);
        })->mapWithKeys(function ($include) use ($request) {
            $resource = call_user_func([$this, $include['method']], $this->resource, $request);
            return [$include['key'] => $resource];
        })->all();
    }
}