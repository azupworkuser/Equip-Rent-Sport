<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserInvitationRequest;
use App\Http\Requests\UpdateUserInvitationRequest;
use App\Http\Resources\UserInvitationResource;
use App\Models\UserInvitation;
use App\CoreLogic\Services\UserInvitationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class UserInvitationController extends Controller
{
    public function __construct(
        public UserInvitationService $userInvitationService,
    ) {
    }

    /**
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return UserInvitationResource::collection(
            $this
                ->userInvitationService
                ->all(
                    paginate: true,
                    allowedFilters: [
                        'email',
                        'status',
                        'team_id'
                    ],
                    allowedSorts: [
                        'email',
                        'status',
                        'team_id'
                    ],
                    load: ['tenant', 'domain', 'team', 'createdBy']
                )
        );
    }


    /**
     * @param  StoreUserInvitationRequest  $request
     * @return JsonResponse
     */
    public function store(StoreUserInvitationRequest $request): JsonResponse
    {
        $userInvitation = $this->userInvitationService->create($request->validated());
        return response()->json([
            'message' => 'User Invitation created successfully',
            'userInvitation' => UserInvitationResource::make($userInvitation),
        ], Response::HTTP_CREATED);
    }

    /**
     * @param  UserInvitation  $userInvitation
     * @return JsonResponse
     */
    public function show(UserInvitation $userInvitation)
    {
        return response()->json(
            UserInvitationResource::make($this->userInvitationService->get($userInvitation)),
            Response::HTTP_OK
        );
    }

    /**
     * @param  UpdateUserInvitationRequest  $request
     * @param  UserInvitation  $userInvitation
     * @return JsonResponse
     */
    public function update(UpdateUserInvitationRequest $request, UserInvitation $userInvitation)
    {
        $userInvitation = $this->userInvitationService->update($request->validated(), $userInvitation);
        return response()->json([
            'message' => 'User Invitation updated successfully',
            'userInvitation' => UserInvitationResource::make($userInvitation),
        ], Response::HTTP_OK);
    }

    /**
     * @param  UserInvitation  $userInvitation
     * @return JsonResponse
     */
    public function destroy(UserInvitation $userInvitation)
    {
        $this->userInvitationService->archive($userInvitation);
        return response()->json([
            'message' => 'User Invitation deleted successfully',
        ], Response::HTTP_OK);
    }
}
