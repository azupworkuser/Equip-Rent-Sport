<?php

namespace Tests\Feature\API\V1;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    public function test_if_login_works()
    {
        $this->postJson('/api/v1/login', [
            'email' => $this->user->email,
            'password' => 'passwoRd@123123',
        ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'token', 'tenants'
            ]);
    }

    public function test_password_reset_request()
    {
        Notification::fake();

        $this->postJson('/api/v1/password/forgot', [
            'email' => $this->user->email,
        ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'message'
            ]);

        Notification::assertCount(1);

        $this->assertDatabaseHas('password_resets', [
            'email' => $this->user->email,
        ]);
    }

    public function test_password_can_be_reset()
    {
        $this->postJson('/api/v1/password/forgot', [
            'email' => $this->user->email,
        ]);
        $token = Str::random(60);
        $hash = \Hash::make($token);

        \DB::table('password_resets')->where('email', $this->user->email)->update([
            'token' => $hash,
        ]);

        $response = $this->postJson('/api/v1/password/reset/', [
            'token' => $token,
            'email' => $this->user->email,
            'password' => 'new-password@123A',
            'password_confirmation' => 'new-password@123A',
        ]);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'message'
            ]);
    }
}
