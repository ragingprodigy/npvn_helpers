<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Tymon\JWTAuth\JWTAuth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $auth;

    /**
     * Controller constructor.
     * @param JWTAuth $auth
     */
    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param $data
     * @param int $code
     * @return JsonResponse
     */
    protected function jsonResponse($data, $code = 200): JsonResponse
    {
        return response()->json($data, $code);
    }

    /**
     * @return JsonResponse
     */
    protected function notFound(): JsonResponse
    {
        return $this->jsonResponse(['message' => 'Not Found'], 404);
    }
}
