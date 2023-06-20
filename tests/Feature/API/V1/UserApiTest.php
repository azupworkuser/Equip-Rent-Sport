<?php

namespace Tests\Feature\API\V1;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use DatabaseMigrations;

    public function test_if_api_returns_authenticated_user()
    {
        $this
            ->shouldPassToken()
            ->getJson('/api/v1/user')
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'name', 'first_name', 'email', 'last_name', 'tenants'
                    ]
                ]
            ]);
    }
}
