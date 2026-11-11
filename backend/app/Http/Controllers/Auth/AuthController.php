<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Enforce server-managed attributes for security
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'], // Automatically hashed by User model casts
            'role' => 'driver',
            'is_active' => true,
        ]);

        // Authenticate the newly registered user in the session
        Auth::guard('web')->login($user);

        // Regenerate session ID to prevent session fixation when session is present
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully.',
            'data' => [
                'user' => $this->sanitizeUser($user),
            ],
        ], 201);
    }

    /**
     * Handle user login.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // Enforce rate limiting before attempting authentication
        $request->ensureIsNotRateLimited();

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember', false);

        // Check if user exists to verify active status specifically
        $user = User::where('email', $credentials['email'])->first();

        if ($user && ! $user->is_active) {
            $request->hitRateLimiter();

            return response()->json([
                'status' => 'error',
                'message' => 'Your account is inactive. Please contact support.',
            ], 403);
        }

        // Attempt authentication via Laravel's web guard
        if (! Auth::guard('web')->attempt($credentials, $remember)) {
            $request->hitRateLimiter();

            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ])->status(401);
        }

        // Clear rate limiter on successful authentication
        $request->clearRateLimiter();

        // Regenerate session ID to prevent session fixation when session is present
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        /** @var User $authenticatedUser */
        $authenticatedUser = Auth::guard('web')->user();

        return response()->json([
            'status' => 'success',
            'message' => 'Authenticated successfully.',
            'data' => [
                'user' => $this->sanitizeUser($authenticatedUser),
            ],
        ], 200);
    }

    /**
     * Return the currently authenticated user's profile.
     */
    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $this->sanitizeUser($user),
            ],
        ], 200);
    }

    /**
     * Log the user out of the application and invalidate the session.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        // Forget user on Sanctum request guard if resolved in the current request lifecycle
        if (Auth::hasResolvedGuards()) {
            try {
                Auth::guard('sanctum')->forgetUser();
            } catch (\Throwable) {
                // Ignore if sanctum guard is not active
            }
        }

        $request->setUserResolver(fn () => null);

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.',
        ], 200);
    }

    /**
     * Send a password reset link to the given user.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $request->ensureIsNotRateLimited();

        $credentials = $request->validated();

        // Use Laravel's built-in password broker to send the reset link
        Password::sendResetLink($credentials);

        $request->hitRateLimiter();

        // Consistent response for registered and unregistered emails to avoid account enumeration
        return response()->json([
            'status' => 'success',
            'message' => 'If an account exists for this email, a password reset link has been sent.',
        ], 200);
    }

    /**
     * Reset the given user's password.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $status = Password::reset(
            $credentials,
            function (User $user, string $password) {
                // Update password (automatically hashed by User model casts)
                $user->password = $password;
                $user->setRememberToken(Str::random(60));
                $user->save();

                // Invalidate existing sessions in database if session driver is database
                if (config('session.driver') === 'database' && Schema::hasTable(config('session.table', 'sessions'))) {
                    DB::table(config('session.table', 'sessions'))
                        ->where('user_id', $user->getAuthIdentifier())
                        ->delete();
                }

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'status' => 'success',
                'message' => 'Password has been reset successfully.',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'The password reset link is invalid or has expired.',
        ], 422);
    }

    /**
     * Return safe user representation excluding sensitive attributes.
     */
    protected function sanitizeUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'is_active' => (bool) $user->is_active,
            'avatar' => $user->avatar,
            'email_verified_at' => $user->email_verified_at?->toISOString(),
            'created_at' => $user->created_at?->toISOString(),
            'updated_at' => $user->updated_at?->toISOString(),
        ];
    }
}
