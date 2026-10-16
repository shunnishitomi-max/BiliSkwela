<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\InventoryTestCase;

class AuthenticationTest extends InventoryTestCase
{
    public function test_authentication_screens_and_guest_restrictions(): void
    {
        foreach (['/login', '/register', '/forgot-password'] as $url) {
            $this->get($url)->assertOk();
        }
        foreach (['/dashboard', '/products', '/categories', '/suppliers', '/stock', '/reports', '/users', '/settings'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
    }

    public function test_registration_assigns_staff_and_hashes_password(): void
    {
        $this->post('/register', ['name' => 'New Student', 'email' => 'new@example.test', 'password' => 'Password123!', 'password_confirmation' => 'Password123!'])->assertRedirect('/dashboard');
        $u = User::where('email', 'new@example.test')->firstOrFail();
        $this->assertTrue($u->hasRole('Staff'));
        $this->assertTrue(Hash::check('Password123!', $u->password));
        $this->assertAuthenticatedAs($u);
    }

    public function test_registration_cannot_assign_an_admin_role(): void
    {
        $this->post('/register', ['name' => 'Escalation', 'email' => 'bad@example.test', 'password' => 'Password123!', 'password_confirmation' => 'Password123!', 'role' => 'Admin'])->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'bad@example.test']);
    }

    public function test_fortify_login_failure_success_and_logout(): void
    {
        $u = $this->actor();
        $this->post('/login', ['email' => $u->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->post('/login', ['email' => $u->email, 'password' => 'Password123!'])->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($u);
        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_login_is_rate_limited(): void
    {
        $u = $this->actor();
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $u->email, 'password' => 'wrong']);
        }
        $this->post('/login', ['email' => $u->email, 'password' => 'wrong'])->assertStatus(429);
    }

    public function test_password_reset_notification_and_token_change_password(): void
    {
        Notification::fake();
        $u = $this->actor();
        $this->post('/forgot-password', ['email' => $u->email])->assertSessionHas('status');
        Notification::assertSentTo($u, ResetPassword::class);
        $token = Password::createToken($u);
        $this->get('/reset-password/'.$token.'?email='.urlencode($u->email))->assertOk();
        $this->post('/reset-password', ['token' => $token, 'email' => $u->email, 'password' => 'NewPassword123!', 'password_confirmation' => 'NewPassword123!'])->assertRedirect('/login');
        $this->assertTrue(Hash::check('NewPassword123!', $u->fresh()->password));
    }

    public function test_invalid_reset_token_does_not_change_password(): void
    {
        $u = $this->actor();
        $this->post('/reset-password', ['token' => 'invalid', 'email' => $u->email, 'password' => 'NewPassword123!', 'password_confirmation' => 'NewPassword123!'])->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check('Password123!', $u->fresh()->password));
    }

    public function test_profile_and_password_updates_require_valid_input(): void
    {
        $u = $this->actor();
        $this->actingAs($u);
        $this->put('/user/profile-information', ['name' => 'Updated Name', 'email' => 'updated@example.test'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['id' => $u->id, 'name' => 'Updated Name']);
        $this->put('/user/password', ['current_password' => 'wrong', 'password' => 'NewPassword123!', 'password_confirmation' => 'NewPassword123!'])->assertSessionHasErrors(['current_password'], null, 'updatePassword');
        $this->put('/user/password', ['current_password' => 'Password123!', 'password' => 'NewPassword123!', 'password_confirmation' => 'NewPassword123!'])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NewPassword123!', $u->fresh()->password));
    }
}
