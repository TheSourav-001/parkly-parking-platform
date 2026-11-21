<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Contracts\Provider as SocialiteProvider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleOAuthTest extends TestCase
{
    use DatabaseTransactions;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Helper to mock Socialite Google driver.
     */
    protected function mockSocialiteUser(?SocialiteUser $socialiteUser = null, ?\Throwable $exception = null): void
    {
        $provider = Mockery::mock(SocialiteProvider::class);

        if ($exception) {
            $provider->shouldReceive('user')->andThrow($exception);
        } elseif ($socialiteUser) {
            $provider->shouldReceive('user')->andReturn($socialiteUser);
        }

        $provider->shouldReceive('scopes')->andReturnSelf();
        $provider->shouldReceive('redirect')->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }

    /**
     * Helper to create a fake Socialite Google user.
     */
    protected function makeSocialiteUser(
        string $id = 'google_123456789',
        string $email = 'google.user@example.com',
        string $name = 'Google User',
        ?string $avatar = 'https://lh3.googleusercontent.com/avatar',
        bool $emailVerified = true
    ): SocialiteUser {
        $user = new SocialiteUser;
        $user->id = $id;
        $user->name = $name;
        $user->email = $email;
        $user->avatar = $avatar;
        $user->user = [
            'id' => $id,
            'email' => $email,
            'email_verified' => $emailVerified,
            'name' => $name,
            'picture' => $avatar,
        ];

        return $user;
    }

    /**
     * Test 1: Google redirect endpoint redirects to OAuth provider.
     */
    public function test_google_redirect_endpoint_redirects_to_provider(): void
    {
        $this->mockSocialiteUser();

        $response = $this->get('/auth/google/redirect');

        $response->assertStatus(302);
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
    }

    /**
     * Test 2, 14, 15, 16, 17, 18: A new verified Google user can register, log in, session ID regenerates,
     * default role is driver, password is securely hashed and not empty, tokens not in URL, and /me works.
     */
    public function test_new_verified_google_user_registers_and_authenticates(): void
    {
        $socialiteUser = $this->makeSocialiteUser('google_new_123', 'new.google.user@example.com', 'Alex Google');
        $this->mockSocialiteUser($socialiteUser);

        $this->withSession(['test_session_id' => 'before_oauth']);
        $initialSessionId = Session::getId();

        $response = $this->get('/auth/google/callback?code=mock_code&state=mock_state');

        // Check redirect location
        $response->assertStatus(302);
        $location = $response->headers->get('Location');
        $this->assertEquals('http://localhost:5173/?google=success', $location);

        // Security check: No tokens or secrets in redirect URL
        $this->assertStringNotContainsString('token', $location);
        $this->assertStringNotContainsString('secret', $location);
        $this->assertStringNotContainsString('password', $location);
        $this->assertStringNotContainsString('google_new_123', $location);

        // Session ID must be regenerated
        $this->assertNotEquals($initialSessionId, Session::getId(), 'Session ID must regenerate upon Google login.');

        // User must exist in database with correct attributes
        $this->assertDatabaseHas('users', [
            'email' => 'new.google.user@example.com',
            'google_id' => 'google_new_123',
            'name' => 'Alex Google',
            'role' => 'driver',
            'is_active' => 1,
        ]);

        $createdUser = User::where('email', 'new.google.user@example.com')->first();
        $this->assertNotNull($createdUser->email_verified_at);
        $this->assertNotEmpty($createdUser->password);
        $this->assertTrue(str_starts_with($createdUser->password, '$2y$'));

        // Verify authenticated in session
        $this->assertAuthenticatedAs($createdUser, 'web');

        // Verify existing /me profile API works
        $meResponse = $this->withHeaders([
            'Origin' => 'http://localhost:5173',
            'Referer' => 'http://localhost:5173/',
            'Accept' => 'application/json',
        ])->getJson('/api/v1/auth/me');

        $meResponse->assertStatus(200)
            ->assertJsonPath('data.user.email', 'new.google.user@example.com')
            ->assertJsonPath('data.user.role', 'driver');
    }

    /**
     * Test 3: Returning Google user logs in seamlessly.
     */
    public function test_returning_google_user_logs_in_successfully(): void
    {
        $existingUser = User::create([
            'name' => 'Existing Google Member',
            'email' => 'returning.google@example.com',
            'google_id' => 'google_returning_456',
            'password' => 'secret_random_pass',
            'role' => 'driver',
            'is_active' => true,
        ]);

        $socialiteUser = $this->makeSocialiteUser('google_returning_456', 'returning.google@example.com');
        $this->mockSocialiteUser($socialiteUser);

        $response = $this->get('/auth/google/callback?code=mock_code&state=mock_state');

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost:5173/?google=success');
        $this->assertAuthenticatedAs($existingUser, 'web');
    }

    /**
     * Test 4, 5, 6: Existing email/password account can be linked; password, role, and phone are preserved.
     */
    public function test_existing_email_account_is_linked_without_overwriting_password_or_role(): void
    {
        $originalPassword = 'MySecretOriginalPassword123!';
        $existingAccount = User::create([
            'name' => 'Manual Register User',
            'email' => 'manual.user@example.com',
            'phone' => '+880 1712-999999',
            'password' => $originalPassword,
            'role' => 'driver',
            'is_active' => true,
            'google_id' => null,
        ]);

        $socialiteUser = $this->makeSocialiteUser('google_link_789', 'manual.user@example.com', 'Updated Name from Google');
        $this->mockSocialiteUser($socialiteUser);

        $response = $this->get('/auth/google/callback?code=mock_code&state=mock_state');

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost:5173/?google=success');

        $existingAccount->refresh();

        // Google ID linked
        $this->assertEquals('google_link_789', $existingAccount->google_id);

        // Password remains original and untouched
        $this->assertTrue(Hash::check($originalPassword, $existingAccount->password));

        // Phone and Role preserved
        $this->assertEquals('+880 1712-999999', $existingAccount->phone);
        $this->assertEquals('driver', $existingAccount->role);
        $this->assertNotNull($existingAccount->email_verified_at);

        $this->assertAuthenticatedAs($existingAccount, 'web');
    }

    /**
     * Test 7: An existing account with a different Google ID cannot be silently relinked.
     */
    public function test_existing_account_with_different_google_id_cannot_be_relinked(): void
    {
        $user = User::create([
            'name' => 'Original User',
            'email' => 'conflict@example.com',
            'google_id' => 'original_google_id_111',
            'password' => 'secret_pass_123',
            'role' => 'driver',
            'is_active' => true,
        ]);

        // Attempt callback with different Google ID for same email
        $socialiteUser = $this->makeSocialiteUser('different_google_id_222', 'conflict@example.com');
        $this->mockSocialiteUser($socialiteUser);

        $response = $this->get('/auth/google/callback?code=mock_code&state=mock_state');

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost:5173/login?google=failed');

        $user->refresh();
        $this->assertEquals('original_google_id_111', $user->google_id, 'Google ID must not be overwritten.');
        $this->assertGuest('web');
    }

    /**
     * Test 8: Inactive user cannot authenticate through Google.
     */
    public function test_inactive_user_cannot_authenticate_through_google(): void
    {
        User::create([
            'name' => 'Banned User',
            'email' => 'banned@example.com',
            'google_id' => 'banned_google_id_333',
            'password' => 'secret_pass_123',
            'role' => 'driver',
            'is_active' => false,
        ]);

        $socialiteUser = $this->makeSocialiteUser('banned_google_id_333', 'banned@example.com');
        $this->mockSocialiteUser($socialiteUser);

        $response = $this->get('/auth/google/callback?code=mock_code&state=mock_state');

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost:5173/login?google=failed');
        $this->assertGuest('web');
    }

    /**
     * Test 9: Unverified Google email is rejected.
     */
    public function test_unverified_google_email_is_rejected(): void
    {
        $socialiteUser = $this->makeSocialiteUser(
            id: 'unverified_google_id',
            email: 'unverified@example.com',
            emailVerified: false
        );
        $this->mockSocialiteUser($socialiteUser);

        $response = $this->get('/auth/google/callback?code=mock_code&state=mock_state');

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost:5173/login?google=failed');
        $this->assertDatabaseMissing('users', ['email' => 'unverified@example.com']);
        $this->assertGuest('web');
    }

    /**
     * Test 10: Missing Google email is rejected.
     */
    public function test_missing_google_email_is_rejected(): void
    {
        $socialiteUser = $this->makeSocialiteUser(
            id: 'no_email_google_id',
            email: ''
        );
        $this->mockSocialiteUser($socialiteUser);

        $response = $this->get('/auth/google/callback?code=mock_code&state=mock_state');

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost:5173/login?google=failed');
        $this->assertGuest('web');
    }

    /**
     * Test 11: Missing Google ID is rejected.
     */
    public function test_missing_google_id_is_rejected(): void
    {
        $socialiteUser = $this->makeSocialiteUser(
            id: '',
            email: 'noid@example.com'
        );
        $this->mockSocialiteUser($socialiteUser);

        $response = $this->get('/auth/google/callback?code=mock_code&state=mock_state');

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost:5173/login?google=failed');
        $this->assertDatabaseMissing('users', ['email' => 'noid@example.com']);
        $this->assertGuest('web');
    }

    /**
     * Test 12: OAuth state mismatch / InvalidStateException is rejected safely.
     */
    public function test_oauth_state_mismatch_is_rejected_safely(): void
    {
        $this->mockSocialiteUser(null, new InvalidStateException('Invalid OAuth state'));

        $response = $this->get('/auth/google/callback?code=mock_code&state=invalid_state');

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost:5173/login?google=failed');
        $this->assertGuest('web');
    }

    /**
     * Test 13: User cancellation or provider error redirect safely.
     */
    public function test_user_cancellation_or_provider_error_redirects_safely(): void
    {
        $response = $this->get('/auth/google/callback?error=access_denied');

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost:5173/login?google=failed');
        $this->assertGuest('web');
    }
}

