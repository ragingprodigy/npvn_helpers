<?php
declare(strict_types=1);

/**
 * Created by: dapo <o.omonayajo@gmail.com>
 * Created on: 7/13/17, 4:37 PM
 */

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{

    /**
     * Do Login
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \InvalidArgumentException
     */
    public function login(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);

        $payload = $request->only('email', 'password');

        if (! $token = $this->auth->attempt($payload)) {
            return response()->json(['user_not_found' => 'You do not have access to this app yet'], 404);
        }

        return response()->json(compact('token'));
    }
}
