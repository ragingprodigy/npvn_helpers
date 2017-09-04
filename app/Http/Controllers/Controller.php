<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Tymon\JWTAuth\Exceptions\JWTException;
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
     * @return false|\Tymon\JWTAuth\Contracts\JWTSubject|User
     * @throws JWTException
     */
    protected function getUser()
    {
        return $this->auth->parseToken()->toUser();
    }

    /**
     * @return mixed
     * @throws JWTException
     */
    protected function getUserId()
    {
        return $this->auth->parseToken()->getPayload()['sub'];
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
