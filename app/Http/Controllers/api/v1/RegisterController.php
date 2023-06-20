<?php

namespace App\Http\Controllers\api\v1;

use App\Actions\CreateTenantAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterStep1Request;
use App\Http\Requests\RegisterStep2Request;
use App\Http\Resources\TenantResource;
use App\Http\Resources\TokenResource;
use App\CoreLogic\Services\DomainService;
use Illuminate\Http\Response;
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function __construct(
        public DomainService $domainService
    ) {
    }

    /**
     * @param RegisterStep1Request $request
     * @return \Illuminate\Http\JsonResponse|never
     */
    public function step1(RegisterStep1Request $request)
    {
        $score = RecaptchaV3::verify($request->get('g-recaptcha-response'), 'register');
        if ($score < 0.7 && app()->environment('production')) {
            return abort(400, 'You are most likely a bot');
        }
        $tenant = (new CreateTenantAction())($request->validated(), $request->get('domain'));

        $user = $tenant->teams->first()->users->first();

        return response()->json([
            'message' => 'Tenant created successfully',
            'tenant' => TenantResource::make($tenant),
            'token' => new TokenResource($user->createToken('default')),
        ], Response::HTTP_CREATED);
    }

    /**
     * @param RegisterStep2Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function step2(RegisterStep2Request $request)
    {
        $tenant = tenant();
        $this->domainService->update($tenant->primary_domain, $request->validated());

        return response()->json([
            'message' => 'Tenant updated successfully',
            'tenant' => TenantResource::make($tenant->fresh()),
        ], Response::HTTP_OK);
    }
}
