<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('test@example.com|127.0.0.1');
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
     * Request helper simulating SPA frontend stateful GET request.
     */
    protected function getSpaJson(string $uri): \Illuminate\Testing\TestResponse
    {
        return $this->withHeaders([
            'Origin' => 'http://localhost:5173',
            'Referer' => 'http://localhost:5173/',
            'Accept' => 'application/json',
        ])->getJson($uri);
    }

    /**
     * Test successful user registration.
     */
    public function test_user_can_register_successfully(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john.register.test@example.com',
            'phone' => '+1234567890',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ];

        $response = $this->postSpaJson('/api/v1/auth/register', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'User registered successfully.',
            ])
            ->assertJsonPath('data.user.name', 'John Doe')
            ->assertJsonPath('data.user.email', 'john.register.test@example.com')
            ->assertJsonPath('data.user.phone', '+1234567890')
            ->assertJsonPath('data.user.role', 'driver')
            ->assertJsonPath('data.user.is_active', true)
            ->assertJsonMissingPath('data.user.password')
            ->assertJsonMissingPath('data.user.remember_token');

        $this->assertDatabaseHas('users', [
            'email' => 'john.register.test@example.com',
            'role' => 'driver',
            'is_active' => 1,
            'google_id' => null,
        ]);

        $user = User::where('email', 'john.register.test@example.com')->first();
        $this->assertTrue(Hash::check('SecurePass123!', $user->password));

        // Ensure user is authenticated
        $this->assertAuthenticatedAs($user);
    }

    /**
     * 1. Test Session ID is regenerated after registration to prevent session fixation.
     */
    public function test_session_id_is_regenerated_after_registration(): void
    {
        // Start a session with a known ID
        $this->withSession(['test_key' => 'initial_value']);
        $initialSessionId = Session::getId();

        $payload = [
            'name' => 'Session Register User',
            'email' => 'session.reg.user@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ];

        $response = $this->postSpaJson('/api/v1/auth/register', $payload);
        $response->assertStatus(201);

        $regeneratedSessionId = Session::getId();

        $this->assertNotEmpty($initialSessionId);
        $this->assertNotEmpty($regeneratedSessionId);
        $this->assertNotEquals($initialSessionId, $regeneratedSessionId, 'Session ID must regenerate upon registration.');
    }

    /**
     * 2. Test Session ID is regenerated after successful login to prevent session fixation.
     */
    public function test_session_id_is_regenerated_after_login(): void
    {
        $user = User::create([
            'name' => 'Session Login User',
            'email' => 'session.login.user@example.com',
            'password' => 'ValidPassword123!',
            'role' => 'driver',
            'is_active' => true,
        ]);

        // Start a session with a known ID
        $this->withSession(['test_key' => 'initial_value']);
        $initialSessionId = Session::getId();

        $response = $this->postSpaJson('/api/v1/auth/login', [
            'email' => 'session.login.user@example.com',
            'password' => 'ValidPassword123!',
        ]);
        $response->assertStatus(200);

        $regeneratedSessionId = Session::getId();

        $this->assertNotEmpty($initialSessionId);
        $this->assertNotEmpty($regeneratedSessionId);
        $this->assertNotEquals($initialSessionId, $regeneratedSessionId, 'Session ID must regenerate upon login.');
    }

    /**
     * 3. Test Remember Me functionality.
     */
    public function test_remember_me_functionality_sets_remember_token(): void
    {
        $user = User::create([
            'name' => 'Remember Me User',
            'email' => 'remember.user@example.com',
            'password' => 'ValidPassword123!',
            'role' => 'driver',
            'is_active' => true,
        ]);

        $this->assertNull($user->remember_token);

        $response = $this->postSpaJson('/api/v1/auth/login', [
            'email' => 'remember.user@example.com',
            'password' => 'ValidPassword123!',
            'remember' => true,
        ]);

        $response->assertStatus(200);

        $user->refresh();
        $this->assertNotNull($user->remember_token, 'Remember token should be generated in database when remember is true.');
        $response->assertCookie(Auth::getRecallerName());
    }

    /**
     * 4. Test password and remember_token are NEVER exposed in API responses (Register & Login).
     */
    public function test_password_and_remember_token_are_never_exposed_in_api_responses(): void
    {
        // 4a: Register response check
        $regResponse = $this->postSpaJson('/api/v1/auth/register', [
            'name' => 'Privacy User',
            'email' => 'privacy.user@example.com',
            'password' => 'VerySecretPassword999!',
            'password_confirmation' => 'VerySecretPassword999!',
        ]);

        $regResponse->assertStatus(201);
        $regJson = $regResponse->getContent();
        $this->assertStringNotContainsString('VerySecretPassword999!', $regJson);
        $this->assertArrayNotHasKey('password', $regResponse->json('data.user'));
        $this->assertArrayNotHasKey('remember_token', $regResponse->json('data.user'));

        // 4b: Login response check
        $loginResponse = $this->postSpaJson('/api/v1/auth/login', [
            'email' => 'privacy.user@example.com',
            'password' => 'VerySecretPassword999!',
            'remember' => true,
        ]);

        $loginResponse->assertStatus(200);
        $loginJson = $loginResponse->getContent();
        $this->assertStringNotContainsString('VerySecretPassword999!', $loginJson);
        $this->assertArrayNotHasKey('password', $loginResponse->json('data.user'));
        $this->assertArrayNotHasKey('remember_token', $loginResponse->json('data.user'));
    }

    /**
     * 5. Test client CANNOT inject role, is_active, google_id or avatar during registration.
     */
    public function test_client_cannot_inject_role_is_active_google_id_or_avatar_during_registration(): void
    {
        $payload = [
            'name' => 'Malicious Injection Tester',
            'email' => 'tamper.user@example.com',
            'password' => 'SecurityPass123!',
            'password_confirmation' => 'SecurityPass123!',
            'role' => 'admin',
            'is_active' => false,
            'google_id' => 'malicious_injected_google_id_999',
            'avatar' => 'https://malicious-site.com/fake-avatar.jpg',
        ];

        $response = $this->postSpaJson('/api/v1/auth/register', $payload);

        $response->assertStatus(201);

        // Verify API response sanitized user ignores malicious inputs
        $response->assertJsonPath('data.user.role', 'driver')
            ->assertJsonPath('data.user.is_active', true)
            ->assertJsonPath('data.user.avatar', null);

        // Verify Database strictly ignores all malicious inputs
        $this->assertDatabaseHas('users', [
            'email' => 'tamper.user@example.com',
            'role' => 'driver',
            'is_active' => 1,
            'google_id' => null,
            'avatar' => null,
        ]);

        $createdUser = User::where('email', 'tamper.user@example.com')->first();
        $this->assertEquals('driver', $createdUser->role);
        $this->assertTrue((bool) $createdUser->is_active);
        $this->assertNull($createdUser->google_id);
        $this->assertNull($createdUser->avatar);
    }

    /**
     * 6. Test optional phone handling (null, omitted) and invalid phone format handling.
     */
    public function test_optional_phone_handling_and_invalid_phone_format(): void
    {
        // 6a: Optional phone omitted completely
        $resOmitted = $this->postSpaJson('/api/v1/auth/register', [
            'name' => 'No Phone User',
            'email' => 'no.phone@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
        $resOmitted->assertStatus(201)
            ->assertJsonPath('data.user.phone', null);

        // 6b: Optional phone passed as null
        $resNull = $this->postSpaJson('/api/v1/auth/register', [
            'name' => 'Null Phone User',
            'email' => 'null.phone@example.com',
            'phone' => null,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
        $resNull->assertStatus(201)
            ->assertJsonPath('data.user.phone', null);

        // 6c: Valid international phone format
        $resValid = $this->postSpaJson('/api/v1/auth/register', [
            'name' => 'Valid Phone User',
            'email' => 'valid.phone@example.com',
            'phone' => '+880 1712-345678',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
        $resValid->assertStatus(201)
            ->assertJsonPath('data.user.phone', '+880 1712-345678');

        // 6d: Invalid phone format (letters/special characters)
        $resInvalidChars = $this->postSpaJson('/api/v1/auth/register', [
            'name' => 'Invalid Phone User',
            'email' => 'invalid.phone@example.com',
            'phone' => 'abc-not-a-phone-!@#$',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
        $resInvalidChars->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);

        // 6e: Phone exceeding max length
        $resTooLong = $this->postSpaJson('/api/v1/auth/register', [
            'name' => 'Too Long Phone User',
            'email' => 'toolong.phone@example.com',
            'phone' => '123456789012345678901234567890',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
        $resTooLong->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    /**
     * Test registration rejects duplicate email.
     */
    public function test_registration_fails_with_duplicate_email(): void
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'existing.user@example.com',
            'password' => 'password123',
            'role' => 'driver',
            'is_active' => true,
        ]);

        $payload = [
            'name' => 'Another User',
            'email' => 'existing.user@example.com',
            'password' => 'Secret999!',
            'password_confirmation' => 'Secret999!',
        ];

        $response = $this->postSpaJson('/api/v1/auth/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test registration rejects invalid passwords.
     */
    public function test_registration_fails_with_invalid_or_mismatched_password(): void
    {
        // Too short
        $response = $this->postSpaJson('/api/v1/auth/register', [
            'name' => 'Short Pass',
            'email' => 'short@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);
        $response->assertStatus(422)->assertJsonValidationErrors(['password']);

        // Mismatch
        $response = $this->postSpaJson('/api/v1/auth/register', [
            'name' => 'Mismatch Pass',
            'email' => 'mismatch@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Different123!',
        ]);
        $response->assertStatus(422)->assertJsonValidationErrors(['password']);
    }

    /**
     * Test successful login.
     */
    public function test_user_can_login_successfully(): void
    {
        $user = User::create([
            'name' => 'Jane Login',
            'email' => 'jane.login@example.com',
            'password' => 'ValidPassword123!',
            'role' => 'driver',
            'is_active' => true,
        ]);

        $response = $this->postSpaJson('/api/v1/auth/login', [
            'email' => 'jane.login@example.com',
            'password' => 'ValidPassword123!',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Authenticated successfully.',
            ])
            ->assertJsonPath('data.user.id', $user->id)
            ->assertJsonPath('data.user.email', 'jane.login@example.com')
            ->assertJsonMissingPath('data.user.password')
            ->assertJsonMissingPath('data.user.remember_token');

        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test login failure with wrong credentials.
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        User::create([
            'name' => 'Target User',
            'email' => 'target.user@example.com',
            'password' => 'CorrectPassword123!',
            'role' => 'driver',
            'is_active' => true,
        ]);

        $response = $this->postSpaJson('/api/v1/auth/login', [
            'email' => 'target.user@example.com',
            'password' => 'WrongPassword!',
        ]);

        $response->assertStatus(401)
            ->assertJsonValidationErrors(['email']);

        $this->assertGuest();
    }

    /**
     * Test inactive user cannot log in.
     */
    public function test_inactive_user_cannot_login(): void
    {
        User::create([
            'name' => 'Inactive User',
            'email' => 'inactive.user@example.com',
            'password' => 'ValidPassword123!',
            'role' => 'driver',
            'is_active' => false,
        ]);

        $response = $this->postSpaJson('/api/v1/auth/login', [
            'email' => 'inactive.user@example.com',
            'password' => 'ValidPassword123!',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Your account is inactive. Please contact support.',
            ]);

        $this->assertGuest();
    }

    /**
     * Test login rate limiting triggers after 5 failed attempts.
     */
    public function test_login_rate_limiting_triggers_after_repeated_failures(): void
    {
        $email = 'bruteforce@example.com';
        RateLimiter::clear(strtolower($email) . '|127.0.0.1');

        for ($i = 0; $i < 5; $i++) {
            $response = $this->postSpaJson('/api/v1/auth/login', [
                'email' => $email,
                'password' => 'WrongPass' . $i,
            ]);
            $response->assertStatus(401);
        }

        // 6th attempt should be blocked by rate limiter
        $response = $this->postSpaJson('/api/v1/auth/login', [
            'email' => $email,
            'password' => 'WrongPass6',
        ]);

        $response->assertStatus(429)
            ->assertJsonValidationErrors(['email']);
    }

    // ==========================================
    // PHASE 3: ME & LOGOUT SECURITY TESTS
    // ==========================================

    /**
     * Phase 3 - 1: Authenticated user can retrieve /me profile.
     */
    public function test_authenticated_user_can_retrieve_me_profile(): void
    {
        $user = User::create([
            'name' => 'Profile User',
            'email' => 'profile.user@example.com',
            'phone' => '+1555123456',
            'password' => 'SecurePass123!',
            'role' => 'driver',
            'is_active' => true,
            'avatar' => null,
        ]);
        $user->email_verified_at = now();
        $user->save();

        $response = $this->actingAs($user, 'web')
            ->getSpaJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => 'Profile User',
                        'email' => 'profile.user@example.com',
                        'phone' => '+1555123456',
                        'role' => 'driver',
                        'is_active' => true,
                        'avatar' => null,
                    ],
                ],
            ]);

        $this->assertNotNull($response->json('data.user.email_verified_at'));
    }

    /**
     * Phase 3 - 2: Unauthenticated user receives HTTP 401 from /me.
     */
    public function test_unauthenticated_user_receives_401_from_me(): void
    {
        $response = $this->getSpaJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }

    /**
     * Phase 3 - 3: Profile response never exposes password, remember_token or sensitive auth data.
     */
    public function test_profile_response_never_exposes_password_or_remember_token(): void
    {
        $user = User::create([
            'name' => 'Sensitive Check User',
            'email' => 'sensitive@example.com',
            'password' => 'TopSecretPassword999!',
            'remember_token' => 'sample_remember_token_secret',
            'role' => 'driver',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user, 'web')
            ->getSpaJson('/api/v1/auth/me');

        $response->assertStatus(200);

        $jsonContent = $response->getContent();
        $this->assertStringNotContainsString('TopSecretPassword999!', $jsonContent);
        $this->assertStringNotContainsString('sample_remember_token_secret', $jsonContent);

        $userKeys = array_keys($response->json('data.user'));
        $this->assertNotContains('password', $userKeys);
        $this->assertNotContains('remember_token', $userKeys);
    }

    /**
     * Phase 3 - 4 & 5: Authenticated user can logout successfully and session is invalidated.
     */
    public function test_authenticated_user_can_logout_successfully_and_invalidates_session(): void
    {
        $user = User::create([
            'name' => 'Logout User',
            'email' => 'logout.user@example.com',
            'password' => 'ValidPassword123!',
            'role' => 'driver',
            'is_active' => true,
        ]);

        $this->withSession(['test_session_item' => 'exists_before_logout']);
        $beforeSessionId = Session::getId();

        $response = $this->actingAs($user, 'web')
            ->postSpaJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Logged out successfully.',
            ]);

        $this->assertGuest('web');

        // Verify session was invalidated (new session ID generated)
        $afterSessionId = Session::getId();
        $this->assertNotEquals($beforeSessionId, $afterSessionId, 'Session ID must change after invalidation.');
        $this->assertFalse(Session::has('test_session_item'), 'Session contents must be cleared on logout.');
    }

    /**
     * Phase 3 - 6: Previously authenticated session cannot access /me after logout.
     */
    public function test_session_cannot_access_me_after_logout(): void
    {
        $user = User::create([
            'name' => 'Session Revoke User',
            'email' => 'session.revoke@example.com',
            'password' => 'ValidPassword123!',
            'role' => 'driver',
            'is_active' => true,
        ]);

        // Step 1: Login via API
        $loginRes = $this->postSpaJson('/api/v1/auth/login', [
            'email' => 'session.revoke@example.com',
            'password' => 'ValidPassword123!',
        ]);
        $loginRes->assertStatus(200);

        // Step 2: Access /me successfully
        $meRes = $this->getSpaJson('/api/v1/auth/me');
        $meRes->assertStatus(200);

        // Step 3: Logout
        $logoutRes = $this->postSpaJson('/api/v1/auth/logout');
        $logoutRes->assertStatus(200);

        // Step 4: Access /me again — must be rejected with 401
        $meAfterLogout = $this->getSpaJson('/api/v1/auth/me');
        $meAfterLogout->assertStatus(401);
    }

    /**
     * Phase 3 - 7: CSRF token is regenerated after logout.
     */
    public function test_csrf_token_is_regenerated_after_logout(): void
    {
        $user = User::create([
            'name' => 'Csrf Test User',
            'email' => 'csrf.test@example.com',
            'password' => 'ValidPassword123!',
            'role' => 'driver',
            'is_active' => true,
        ]);

        $this->withSession([]);
        $tokenBefore = Session::token();

        $response = $this->actingAs($user, 'web')
            ->postSpaJson('/api/v1/auth/logout');

        $response->assertStatus(200);

        $tokenAfter = Session::token();
        $this->assertNotEmpty($tokenBefore);
        $this->assertNotEmpty($tokenAfter);
        $this->assertNotEquals($tokenBefore, $tokenAfter, 'CSRF token must regenerate upon logout.');
    }

    /**
     * Phase 3 - 8: Existing Registration and Login functionality continues working after Phase 3 changes.
     */
    public function test_regression_registration_and_login_flow(): void
    {
        $regData = [
            'name' => 'Regression User',
            'email' => 'regression.flow@example.com',
            'password' => 'Regression123!',
            'password_confirmation' => 'Regression123!',
        ];

        // Register
        $regRes = $this->postSpaJson('/api/v1/auth/register', $regData);
        $regRes->assertStatus(201);

        // Access /me immediately after registration
        $meRes = $this->getSpaJson('/api/v1/auth/me');
        $meRes->assertStatus(200)
            ->assertJsonPath('data.user.email', 'regression.flow@example.com');

        // Logout
        $logoutRes = $this->postSpaJson('/api/v1/auth/logout');
        $logoutRes->assertStatus(200);

        // Login again
        $loginRes = $this->postSpaJson('/api/v1/auth/login', [
            'email' => 'regression.flow@example.com',
            'password' => 'Regression123!',
        ]);
        $loginRes->assertStatus(200);

        // Verify /me works after fresh login
        $meAgain = $this->getSpaJson('/api/v1/auth/me');
        $meAgain->assertStatus(200)
            ->assertJsonPath('data.user.email', 'regression.flow@example.com');
    }
}
