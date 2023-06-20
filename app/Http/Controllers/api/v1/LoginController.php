<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\TenantResource;
use App\Http\Resources\TokenResource;
use Illuminate\Http\Response;

class LoginController extends Controller
{
    /**
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (! auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'Email/Password combination is incorrect',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = auth()->user()->createToken($request->device_name ?? 'default');

        return response()->json([
            'token' => TokenResource::make($token),
            'tenants' => TenantResource::collection(auth()->user()->tenants()),
        ], Response::HTTP_OK);
    }
}
