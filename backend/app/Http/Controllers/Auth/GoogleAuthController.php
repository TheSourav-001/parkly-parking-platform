<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function callback(Request $request): RedirectResponse
    {
        $frontendUrl = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173')), '/');

        // Check if user denied access or an error was returned from Google
        if ($request->has('error') || ! $request->has('code')) {
            return redirect()->to("{$frontendUrl}/login?google=failed");
        }

        try {
            /** @var \Laravel\Socialite\Two\User $googleUser */
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            Log::warning('Google OAuth callback failed', [
                'exception' => $e->getMessage(),
            ]);

            return redirect()->to("{$frontendUrl}/login?google=failed");
        }

        // Validate Google identity fields
        $googleId = $googleUser->getId();
        $email = $googleUser->getEmail();
        $rawUser = $googleUser->getRaw();
        $emailVerified = ! empty($rawUser['email_verified']) && ($rawUser['email_verified'] === true || $rawUser['email_verified'] === 'true' || $rawUser['email_verified'] === 1);

        if (empty($googleId) || empty($email) || ! $emailVerified) {
            Log::warning('Google OAuth rejected due to missing identity or unverified email', [
                'has_google_id' => ! empty($googleId),
                'has_email' => ! empty($email),
                'email_verified' => $emailVerified,
            ]);

            return redirect()->to("{$frontendUrl}/login?google=failed");
        }

        $user = null;

        try {
            $user = DB::transaction(function () use ($googleId, $email, $googleUser) {
                // Case A: Existing Google-linked account
                $userByGoogleId = User::where('google_id', $googleId)->first();
                if ($userByGoogleId) {
                    if (! $userByGoogleId->is_active) {
                        return null;
                    }

                    // Optionally update avatar if not set
                    if (empty($userByGoogleId->avatar) && $googleUser->getAvatar()) {
                        $userByGoogleId->avatar = $googleUser->getAvatar();
                        $userByGoogleId->save();
                    }

                    return $userByGoogleId;
                }

                // Case B: Existing email/password account
                $userByEmail = User::where('email', $email)->first();
                if ($userByEmail) {
                    if (! $userByEmail->is_active) {
                        return null;
                    }

                    // If account already has a DIFFERENT non-null google_id, reject silently
                    if (! empty($userByEmail->google_id) && $userByEmail->google_id !== $googleId) {
                        Log::warning('Google OAuth link rejected: account has a different Google ID', [
                            'user_id' => $userByEmail->id,
                        ]);
                        return null;
                    }

                    // Link verified Google ID to existing account
                    $userByEmail->google_id = $googleId;
                    if (empty($userByEmail->avatar) && $googleUser->getAvatar()) {
                        $userByEmail->avatar = $googleUser->getAvatar();
                    }
                    if (is_null($userByEmail->email_verified_at)) {
                        $userByEmail->email_verified_at = now();
                    }
                    $userByEmail->save();

                    return $userByEmail;
                }

                // Case C: New Google user
                $name = $googleUser->getName() ?: (explode('@', $email)[0] ?: 'Parkly User');

                $newUser = new User([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Str::random(40), // Automatically hashed by User model casts
                    'role' => 'driver',
                    'is_active' => true,
                ]);
                $newUser->email_verified_at = now();
                $newUser->save();

                return $newUser;
            });
        } catch (Throwable $e) {
            Log::error('Google OAuth transaction error', [
                'exception' => $e->getMessage(),
            ]);

            return redirect()->to("{$frontendUrl}/login?google=failed");
        }

        if (! $user || ! $user->is_active) {
            return redirect()->to("{$frontendUrl}/login?google=failed");
        }

        // Authenticate the user into the web session
        Auth::guard('web')->login($user);

        // Regenerate session ID to prevent session fixation
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return redirect()->to("{$frontendUrl}/?google=success");
    }
}
