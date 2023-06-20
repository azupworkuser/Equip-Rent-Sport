<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApiKeyRequest;
use App\Http\Resources\ApiKeyResource;
use App\Models\ApiKey;
use App\CoreLogic\Services\ApiKeyService;
use App\CoreLogic\Services\UserService;
use Illuminate\Http\Response;

class ApiKeyController extends Controller
{
    public function __construct(
        public ApiKeyService $apiKeyService,
        public UserService $userService
    ) {
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ApiKeyResource::collection(
            $this->apiKeyService->all(paginate: true)
        );
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreApiKeyRequest $request)
    {
        return response()->json([
            'message' => 'API key created successfully',
            'apiKey' => ApiKeyResource::make(
                $this->apiKeyService->create($request->validated(), currentSubdomain())
            ),
        ], Response::HTTP_CREATED);
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ApiKey $apiKey)
    {
        $apiKey->delete();
        return response()->json([
            'message' => 'Deleted Api key!!',
        ], Response::HTTP_OK);
    }
}
