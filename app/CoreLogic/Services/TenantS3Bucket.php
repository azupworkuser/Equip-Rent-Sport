<?php

namespace App\CoreLogic\Services;

use App\Models\Tenant;
use Aws\Exception\CredentialsException;
use Aws\Result;
use Aws\S3\S3Client;
use Illuminate\Support\Arr;

class TenantS3Bucket
{
    protected S3Client $client;
    public array $policies = [];
    public Tenant $tenant;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
        $this->init();
    }

    /**
     * @return void
     */
    protected function init(): void
    {
        $this->client = $this->getClient();

        if (app()->environment('production') && ! $this->ping()) {
            throw new \RuntimeException('S3 credentials are not correct');
        }
    }

    /**
     * @return bool
     */
    public function ping(): bool
    {
        try {
            $this->client->listBuckets();
            return true;
        } catch (CredentialsException $e) {
            return false;
        }
    }

    /**
     * @return Result
     * @throws \JsonException
     */
    public function create(): Result
    {
        $bucketName = $this->getOrGenerateBucketName();
        $bucketPolicy = $this->policies;

        $result = $this->client->createBucket([
            'Bucket' => $bucketName,
            'Policy' => json_encode($bucketPolicy, JSON_THROW_ON_ERROR),
        ]);

        $this->client->waitUntil('BucketExists', ['Bucket' => $bucketName]);

        return $result;
    }

    /**
     * @return string
     */
    public function getOrGenerateBucketName(): string
    {
        if (method_exists($this->tenant, 'getOrGenerateBucketName')) {
            return $this->tenant->getOrGenerateBucketName();
        }

        return $this->tenant->getKey();
    }

    /**
     * @return S3Client
     */
    protected function getClient(): S3Client
    {
        return new S3Client(
            $this->formatS3Config(
                config($this->getConfigName())
            )
        );
    }

    /**
     * @param array $config
     * @return array|string[]
     */
    protected function formatS3Config(array $config): array
    {
        $config += ['version' => 'latest'];

        if (!empty($config['key']) && !empty($config['secret'])) {
            $config['credentials'] = Arr::only($config, ['key', 'secret', 'token']);
        }

        return $config;
    }

    /**
     * @return string
     */
    public function getConfigName(): string
    {
        return 'filesystems.disks.tenant';
    }

    /**
     * @param string $bucketName
     * @return void
     */
    private function configureObjectHoldForBucket(string $bucketName): void
    {
        $this->client->putObjectLockConfiguration([
            'Bucket' => $bucketName,
            'ChecksumAlgorithm' => 'SHA256',
            'ObjectLockConfiguration' => [
                'ObjectLockEnabled' => 'Enabled',
                'Rule' => [
                    'DefaultRetention' => [
                        'Mode' => 'COMPLIANCE',
                        'Years' => 100,
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param $tenant
     * @return Result
     */
    public function delete($tenant): Result
    {
        return $this->client->deleteBucket([
            'Bucket' => $tenant->getKey()
        ]);
    }
}
