<?php

namespace App\CoreLogic\Services;

use App\Models\ApiKey;
use App\CoreLogic\Repositories\ApiKeyRepository;
use Illuminate\Support\Str;

class ApiKeyService extends Service
{
    protected string $repositoryName = ApiKeyRepository::class;

    /**
     * @param array $data
     * @param $domain
     * @return ApiKey
     */
    public function create(array $data, $domain): ApiKey
    {
        $apiKey = $this->repository->create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => tenant()->id,
            'domain_id' => $domain,
            'name' => $data['name'],
            'key' => $this->generateApiKey(),
            'data' => collect($data)->only(ApiKey::getCustomColumns())->toArray()
        ]);
        $apiKey->syncPermissions($data['permissions']);
        return $apiKey;
    }

    /**
     * @return string
     */
    public function generateApiKey(): string
    {
        return config('hashing.prefix') . hash_hmac('ripemd160', Str::random(40), tenant()->getKey());
    }
}
