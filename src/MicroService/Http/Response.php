<?php

namespace CrCms\Foundation\MicroService\Http;

use CrCms\Foundation\MicroService\Contracts\ResponseContract;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
//use Symfony\Component\HttpFoundation\Response as BaseResponse;

/**
 * Class Response
 * @package CrCms\Foundation\MicroService\Http
 */
class Response extends JsonResponse implements ResponseContract
{
//    public function __construct($data = null, int $status = 200, array $headers = [], int $options = 0)
//    {
//        parent::__construct($data, $status, $headers, $options);
//    }

//    public function send(): void
//    {
//    }
//
//    protected function resolveResponse()
//    {
////        if ($response instanceof Responsable) {
////            $response = $response->toResponse($request);
////        }
//
//        if ($response instanceof PsrResponseInterface) {
//            $response = (new HttpFoundationFactory)->createResponse($response);
//        } elseif ($response instanceof Model && $response->wasRecentlyCreated) {
//            $response = new JsonResponse($response, 201);
//        } elseif (!$response instanceof SymfonyResponse &&
//            ($response instanceof Arrayable ||
//                $response instanceof Jsonable ||
//                $response instanceof ArrayObject ||
//                $response instanceof JsonSerializable ||
//                is_array($response))) {
//            $response = new JsonResponse($response);
//        } elseif (!$response instanceof SymfonyResponse) {
//            $response = new Response($response);
//        }
//
//        if ($response->getStatusCode() === Response::HTTP_NOT_MODIFIED) {
//            $response->setNotModified();
//        }
//
//        return $response->prepare($request);
//    }
}