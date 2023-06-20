<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\CoreLogic\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class PermissionController extends Controller
{
    public function __construct(
        public PermissionService $permissionService,
    ) {
    }

    /**
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return PermissionResource::collection(
            $this
                ->permissionService
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
     * @param  Permission  $permission
     * @return JsonResponse
     */
    public function show(Permission $permission)
    {
        return response()->json(
            PermissionResource::make($this->permissionService->get($permission)),
            Response::HTTP_OK
        );
    }
}
