<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\CoreLogic\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(
        public UserService $userService,
    ) {
    }

    /**
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return UserResource::collection(
            $this
                ->userService
                ->all(
                    paginate: true,
                    allowedFilters: [
                        'email',
                    ],
                    allowedSorts: [
                        'email',
                    ]
                )
        );
    }


    /**
     * @param  StoreUserRequest  $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());
        return response()->json([
            'message' => 'User created successfully',
            'user' => UserResource::make($user),
        ], Response::HTTP_CREATED);
    }

    /**
     * @param  UpdateUserRequest  $request
     * @param  User  $user
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user = $this->userService->update($request->validated(), $user);
        return response()->json([
            'message' => 'User updated successfully',
            'user' => UserResource::make($user),
        ], Response::HTTP_OK);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        return response()->json(
            UserResource::make($this->userService->find($id)),
            Response::HTTP_OK
        );
    }

    /**
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = auth()->user();
        if (!Hash::check($request->oldPassword, $user->password)) {
            return response()->json([
                'message' => 'Password does not match',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $data = $request->only(['newPassword']);
        $user->update(['password' => $data['newPassword']]);
        return response()->json(
            [
                "message" => "Password changed successfully"
            ],
            Response::HTTP_OK
        );
    }
}
