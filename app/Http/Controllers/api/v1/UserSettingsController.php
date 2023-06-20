<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
//use App\Http\Resources\TenantResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\CoreLogic\Services\UserService;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UploadImageRequest;
use Illuminate\Http\Response;

class UserSettingsController extends Controller
{
    public function __construct(
        public UserService $userService,
    ) {
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $userSettings = auth()->user();
        $userSettings['tenant'] = tenant();
        return response()->json($userSettings, Response::HTTP_OK);
    }

    /**
     * @param UpdateUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request)
    {
        $user     = auth()->user();
        if ($request->password && !Hash::check($request->old_password, $user->password)) {
            return response()->json([
            'message' => 'Password does not match',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $data = $request->only(['first_name', 'last_name', 'password', 'email']);
        $user->update($data);
        return response()->json([
            'message' => 'Successfully Updated!!'
        ], Response::HTTP_OK);
    }

    /**
     * @param UploadImageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadProfileImage(UploadImageRequest $request)
    {
        if (!$request->hasFile('image')) {
            return response()->json(['message' => 'Missing file'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $fileImageUrl = $this->userService->uploadProfileImage($request);
        auth()->user()->update(['profile_url' => $fileImageUrl]);
        return response()->json([
            'message' => 'Updated profile image',
            'url' => $fileImageUrl,
        ], Response::HTTP_OK);
    }
}
