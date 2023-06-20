<?php

namespace Tests\Feature\API\V1;

use App\Models\Permission;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;


    public function test_if_list_of_all_permissions_are_returned()
    {
        $count = count(Permission::PERMISSIONS_NEW);
        $response = $this
            ->actingAs($this->user, 'api')
            ->getJson('/api/v1/permission', [
                'X-Tenant' => $this->tenant->getKey()
            ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'data'
            ])->assertJsonCount($count, 'data');
    }
}
