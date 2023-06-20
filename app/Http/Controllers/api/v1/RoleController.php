<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\CoreLogic\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    public function __construct(
        public RoleService $roleService,
    ) {
    }

    /**
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return RoleResource::collection(
            $this
                ->roleService
                ->all(
                    paginate: true,
                    allowedFilters: [
                        'name'
                    ],
                    allowedSorts: [
                        'name'
                    ]
                )
        );
    }

    /**
     * @param  StoreRoleRequest  $request
     * @return JsonResponse
     */
    public function store(StoreRoleRequest $request)
    {
        $role = $this->roleService->create($request->validated());
        return response()->json([
            'message' => 'Role created successfully',
            'role' => RoleResource::make($role),
        ], Response::HTTP_CREATED);
    }

    /**
     * @param  Role  $role
     * @return JsonResponse
     */
    public function show(Role $role)
    {
        return response()->json(
            RoleResource::make($this->roleService->get($role)),
            Response::HTTP_OK
        );
    }

    /**
     * @param  UpdateRoleRequest  $request
     * @param  Role  $role
     * @return JsonResponse
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role = $this->roleService->update($request->validated(), $role);
        return response()->json([
            'message' => 'Role updated successfully',
            'role' => RoleResource::make($role),
        ], Response::HTTP_OK);
    }

    /**
     * @param  Role  $role
     * @return JsonResponse
     */
    public function destroy(Role $role): JsonResponse
    {
        $this->roleService->archive($role);
        return response()->json([
            'message' => 'Role deleted successfully',
        ], Response::HTTP_OK);
    }
}
