<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class PasswordResetApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('reset.test@example.com|127.0.0.1');
    }

    /**
     * Request helper simulating SPA frontend stateful POST request.
     */
    protected function postSpaJson(string $uri, array $data = []): \Illuminate\Testing\TestResponse
    {
        return $this->withHeaders([
            'Origin' => 'http://localhost:5173',
            'Referer' => 'http://localhost:5173/',
            'Accept' => 'application/json',
        ])->postJson($uri, $data);
    }

    /**
     * Test 1: Registered user receives a password reset notification.
     */
    public function test_registered_user_receives_password_reset_notification(): void
    {
        Notification::fake();

        $user = User::create([
            'name' => 'Reset Test User',
            'email' => 'reset.test@example.com',
            'password' => 'InitialPassword123!',
            'role' => 'driver',
            'is_active' => true,
        ]);

        $response = $this->postSpaJson('/api/v1/auth/forgot-password', [
            'email' => 'reset.test@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'If an account exists for this email, a password reset link has been sent.',
            ]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    /**
     * Test 2 & 3: Unregistered email receives the SAME generic response, avoiding email enumeration.
     */
    public function test_unregistered_email_receives_same_generic_response_without_disclosure(): void
    {
        Notification::fake();

        $response = $this->postSpaJson('/api/v1/auth/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'If an account exists for this email, a password reset link has been sent.',
            ]);

        Notification::assertNothingSent();
    }

    /**
     * Test 4: Invalid email receives validation error.
     */
    public function test_forgot_password_invalid_email_receives_validation_error(): void
    {
        $response = $this->postSpaJson('/api/v1/auth/forgot-password', [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test 5: Forgot-password endpoint respects rate limiting / throttling.
     */
    public function test_forgot_password_endpoint_respects_throttling(): void
    {
        $email = 'throttle.user@example.com';
        RateLimiter::clear(strtolower($email) . '|127.0.0.1');

        for ($i = 0; $i < 5; $i++) {
            $response = $this->postSpaJson('/api/v1/auth/forgot-password', [
                'email' => $email,
            ]);
            $response->assertStatus(200);
        }

        // 6th attempt should trigger 429
        $response = $this->postSpaJson('/api/v1/auth/forgot-password', [
            'email' => $email,
        ]);

        $response->assertStatus(429)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test 6, 7, 8, 16, 17: Valid reset token successfully updates password, works with login,
     * old password no longer works, remember_token is rotated, and PasswordReset event is dispatched.
     */
    public function test_valid_reset_token_updates_password_rotates_token_and_dispatches_event(): void
    {
        Notification::fake();
        Event::fake([PasswordReset::class]);

        $user = User::create([
            'name' => 'Full Reset User',
            'email' => 'fullreset@example.com',
            'password' => 'OldPassword123!',
            'role' => 'driver',
            'is_active' => true,
        ]);
        $oldRememberToken = 'old_initial_remember_token_string';
        $user->setRememberToken($oldRememberToken);
        $user->save();

        // Step 1: Request reset link
        $forgotResponse = $this->postSpaJson('/api/v1/auth/forgot-password', [
            'email' => 'fullreset@example.com',
        ]);
        $forgotResponse->assertStatus(200);

        // Step 2: Intercept token from notification
        $resetToken = null;
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use (&$resetToken, $user) {
            $resetToken = $notification->token;
            // Verify notification URL format matches Vue SPA
            $url = $notification->toMail($user)->actionUrl;
            $this->assertStringContainsString('http://localhost:5173/reset-password?token=', $url);
            $this->assertStringContainsString('&email=' . urlencode('fullreset@example.com'), $url);
            return true;
        });

        $this->assertNotEmpty($resetToken, 'Reset token must be captured from the sent notification.');

        // Step 3: Perform reset
        $resetResponse = $this->postSpaJson('/api/v1/auth/reset-password', [
            'token' => $resetToken,
            'email' => 'fullreset@example.com',
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $resetResponse->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Password has been reset successfully.',
            ]);

        // Step 4: Verify PasswordReset event dispatched
        Event::assertDispatched(PasswordReset::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });

        // Step 5: Verify new password works in DB and old is rejected
        $user->refresh();
        $this->assertTrue(Hash::check('NewSecurePassword123!', $user->password));
        $this->assertFalse(Hash::check('OldPassword123!', $user->password));

        // Step 6: Verify remember_token is rotated
        $this->assertNotEquals($oldRememberToken, $user->remember_token);

        // Step 7: Verify login with new password succeeds
        $loginNewRes = $this->postSpaJson('/api/v1/auth/login', [
            'email' => 'fullreset@example.com',
            'password' => 'NewSecurePassword123!',
        ]);
        $loginNewRes->assertStatus(200);

        // Step 8: Verify login with old password fails
        $loginOldRes = $this->postSpaJson('/api/v1/auth/login', [
            'email' => 'fullreset@example.com',
            'password' => 'OldPassword123!',
        ]);
        $loginOldRes->assertStatus(401);
    }

    /**
     * Test 9: Password confirmation mismatch is rejected.
     */
    public function test_reset_password_fails_when_confirmation_mismatches(): void
    {
        $response = $this->postSpaJson('/api/v1/auth/reset-password', [
            'token' => 'dummy_token',
            'email' => 'mismatch@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test 10: Password shorter than 8 characters is rejected.
     */
    public function test_reset_password_fails_when_password_is_short(): void
    {
        $response = $this->postSpaJson('/api/v1/auth/reset-password', [
            'token' => 'dummy_token',
            'email' => 'short@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test 11: Invalid token is rejected with HTTP 422.
     */
    public function test_reset_password_fails_with_invalid_token(): void
    {
        $user = User::create([
            'name' => 'Invalid Token User',
            'email' => 'invalid.token@example.com',
            'password' => 'ValidPassword123!',
            'role' => 'driver',
            'is_active' => true,
        ]);

        $response = $this->postSpaJson('/api/v1/auth/reset-password', [
            'token' => 'completely_invalid_random_token_12345',
            'email' => 'invalid.token@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'The password reset link is invalid or has expired.',
            ]);
    }

    /**
     * Test 12: Expired token is rejected.
     */
    public function test_reset_password_fails_with_expired_token(): void
    {
        $user = User::create([
            'name' => 'Expired User',
            'email' => 'expired@example.com',
            'password' => 'ValidPassword123!',
            'role' => 'driver',
            'is_active' => true,
        ]);

        // Create token directly using Password broker
        $token = Password::broker()->createToken($user);

        // Manually age the token in password_reset_tokens table beyond expiry (60 mins)
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', 'expired@example.com')
            ->update(['created_at' => now()->subMinutes(120)]);

        $response = $this->postSpaJson('/api/v1/auth/reset-password', [
            'token' => $token,
            'email' => 'expired@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'The password reset link is invalid or has expired.',
            ]);
    }

    /**
     * Test 13: Token associated with another email is rejected.
     */
    public function test_reset_password_fails_when_token_belongs_to_another_email(): void
    {
        $user1 = User::create([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'password' => 'ValidPassword123!',
            'role' => 'driver',
            'is_active' => true,
        ]);

        $user2 = User::create([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'password' => 'ValidPassword123!',
            'role' => 'driver',
            'is_active' => true,
        ]);

        $tokenForUser1 = Password::broker()->createToken($user1);

        // Attempt to use User 1's token with User 2's email
        $response = $this->postSpaJson('/api/v1/auth/reset-password', [
            'token' => $tokenForUser1,
            'email' => 'user2@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'The password reset link is invalid or has expired.',
            ]);
    }

    /**
     * Test 14: Reusing an already-consumed token is rejected.
     */
    public function test_reusing_consumed_token_is_rejected(): void
    {
        $user = User::create([
            'name' => 'Single Use User',
            'email' => 'singleuse@example.com',
            'password' => 'ValidPassword123!',
            'role' => 'driver',
            'is_active' => true,
        ]);

        $token = Password::broker()->createToken($user);

        // First use: should succeed
        $firstUseRes = $this->postSpaJson('/api/v1/auth/reset-password', [
            'token' => $token,
            'email' => 'singleuse@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);
        $firstUseRes->assertStatus(200);

        // Second use: must fail
        $secondUseRes = $this->postSpaJson('/api/v1/auth/reset-password', [
            'token' => $token,
            'email' => 'singleuse@example.com',
            'password' => 'AnotherNewPassword123!',
            'password_confirmation' => 'AnotherNewPassword123!',
        ]);
        $secondUseRes->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'The password reset link is invalid or has expired.',
            ]);
    }

    /**
     * Test 15: Password and reset token are NEVER exposed in API responses.
     */
    public function test_password_and_reset_token_never_exposed_in_api_responses(): void
    {
        $user = User::create([
            'name' => 'No Leak User',
            'email' => 'noleak@example.com',
            'password' => 'OldPassword123!',
            'role' => 'driver',
            'is_active' => true,
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->postSpaJson('/api/v1/auth/reset-password', [
            'token' => $token,
            'email' => 'noleak@example.com',
            'password' => 'SuperSecretNewPassword123!',
            'password_confirmation' => 'SuperSecretNewPassword123!',
        ]);

        $response->assertStatus(200);
        $content = $response->getContent();

        $this->assertStringNotContainsString('SuperSecretNewPassword123!', $content);
        $this->assertStringNotContainsString($token, $content);
    }
}

