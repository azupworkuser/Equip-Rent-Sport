<?php

namespace Tests;

use App\Actions\CreateTenantAction;
use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Fake tenant with correct setup
     * @var Tenant $tenant
     */
    public Tenant $tenant;


    /**
     * domain which belongs to fake tenant
     * @var Domain $domain
     */
    public Domain $domain;

    /**
     * User which belongs to fake tenant's admin
     * @var User $user
     */
    public User $user;

    public array $permissions = ['*'];
    public bool $passToken = true;

    /**
     * Most tests don't need this. Unless they test the billing page.
     *
     * @var bool
     */
    protected $createStripeCustomer = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = $this->createTenant([], null, $this->createStripeCustomer);
        $this->domain = $this->tenant->primary_domain;
        $this->user = $this->tenant->teams->first()->users->first();

        $this->withHeaders([
            'X-Tenant' => $this->tenant->getKey(),
            'X-Subdomain' => $this->domain->getKey(),
        ]);
    }

    /**
     * Call the given URI with a JSON request.
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function json($method, $uri, array $data = [], array $headers = [])
    {
        if ($this->passToken) {
            $this->withHeaders([
                'Authorization' => 'Bearer ' . $this->user->createToken('test', $this->permissions)->plainTextToken
            ]);
        }
        return parent::json($method, $uri, $data, $headers);
    }

    public function shouldPassToken(bool $shouldPass = true, array $permissionNames = ['*']): self
    {
        $this->passToken = $shouldPass;
        $this->permissions = $permissionNames;

        return $this;
    }

    protected function createTenant(array $data = [], string $domain = null, bool $createStriperCustomer = null): Tenant
    {
        $domain = $domain ?? Str::random('10');
        $this->seed(PermissionsSeeder::class);

        return (new CreateTenantAction())(array_merge([
            'company' => 'Foo company',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'foo@tenant.localhost' . uniqid(),
            'password' => 'passwoRd@123123',
        ], $data), $domain, false);
    }
}
