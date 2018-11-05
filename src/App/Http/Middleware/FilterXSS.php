<?php

namespace CrCms\Foundation\App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

/**
 * Class FilterXss
 * @package CrCms\Foundation\App\Http\Middleware
 */
class FilterXSS
{
    /**
     * @param $request
     * @param Closure $next
     * @param bool|string|int $filter
     * @return Response
     */
    public function handle($request, Closure $next, $filter = true)
    {
        /* @var Response|JsonResponse $response */
        $response = $next($request);

        return tap($response, function ($response) use ($request, $filter) {
            /* @var Response|JsonResponse $response */

            if (!$this->isFilter($filter)) {
                return;
            }

            $response instanceof JsonResponse ?
                $response->setData($this->filter($response->getData(true))) :
                $response->setContent($this->filter($response->getContent()));
        });
    }

    /**
     * @param bool|string|int $filter
     * @return bool
     */
    protected function isFilter($filter): bool
    {
        if ($filter === 'false') {
            $filter = false;
        } elseif ($filter === 'true') {
            $filter = true;
        } else {
            $filter = boolval($filter);
        }

        return config('app.filter_xss', $filter);
    }

    /**
     * @param $content
     * @return mixed
     */
    protected function filter($content)
    {
        if (is_array($content)) {
            foreach ($content as $key => $value) {
                $content[$key] = $this->filter($value);
            }
        } elseif ($content instanceof Collection) {
            $content = $content->map(function ($item) {
                return $this->filter($item);
            });
        } elseif (is_string($content) && !is_numeric($content)) {
            $content = e($content);
        } else {
            //$content;
        }

        return $content;
    }
}