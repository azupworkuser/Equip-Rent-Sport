<?php

namespace Tests\Feature\API\V1;

use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Actions\CreateTenantAction;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_update()
    {
        $email = $this->faker->email;
        $payload = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'password_confirmation' => 'passwoRd@123123',
            'password' => 'passwoRd@123123',
            'old_password' => 'passwoRd@123123',
            'email' => $email,
            'domainRoles' => [
                'domain_id' => $this->domain->getKey(),
                'role_id' => Role::all()->random()->getKey()
            ]
        ];

        $this
            ->putJson('/api/v1/user', $payload)
            ->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'email' => $email
        ]);
    }

    public function test_fetch__user_settings()
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

    public function test_upload_profile_image()
    {
        Storage::fake('s3');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $response = $this
            ->shouldPassToken()
            ->postJson('/api/v1/upload-profile-image', [
                'image' => $file,
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'profile_url' => $response->json()['url']
        ]);
    }

    public function test_if_password_changed()
    {
        $this->withoutExceptionHandling();
        $response = $this
            ->shouldPassToken()
            ->putJson('/api/v1/user/change-password', [
                'oldPassword' => 'passwoRd@123123',
                'newPassword' => 'new-password@123A',
                'confirmNewPassword' => 'new-password@123A'
            ])->assertStatus(200)
            ->assertJsonStructure([
                'message'
            ]);
        $this->user->refresh();
        $this->assertTrue(Hash::check('new-password@123A', $this->user->password));
    }

    public function test_if_existing_password_mismatch()
    {
        $this->withoutExceptionHandling();
        $this
            ->actingAs($this->user, 'api')
            ->putJson('/api/v1/user/change-password', [
                'oldPassword' => 'passwoRd@123121',
                'newPassword' => 'new-password@123A',
                'confirmNewPassword' => 'new-password@123A'
            ], [
                'Authorization' => 'Bearer ' . $this->user->createToken('test')->plainTextToken,
            ])
        ->assertStatus(422)
        ->assertJsonStructure([
            'message'
        ]);
        $this->user->refresh();
        $this->assertFalse(Hash::check('new-password@123A', $this->user->password));
    }

    public function test_if_new_and_confirm_password_mismatch()
    {
        $this
            ->actingAs($this->user, 'api')
            ->putJson('/api/v1/user/change-password', [
                'oldPassword' => 'passwoRd@123123',
                'newPassword' => 'new-password@123A',
                'confirmNewPassword' => 'error-password@123A'
            ], [
                'Authorization' => 'Bearer ' . $this->user->createToken('test')->plainTextToken,
            ])
            ->assertStatus(422)
            ->assertJsonStructure([
                 'message',
                 'errors' => [
                     'confirmNewPassword'
                 ]
             ]);
    }
}
