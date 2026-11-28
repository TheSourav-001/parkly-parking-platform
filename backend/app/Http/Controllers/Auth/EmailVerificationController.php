<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * Send email verification notification to the authenticated user.
     */
    public function sendVerificationEmail(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Your email address is already verified.',
                'already_verified' => true,
            ], 200);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'status' => 'success',
            'message' => 'Verification email sent successfully. Please check your inbox.',
        ], 200);
    }

    /**
     * Verify email via signed URL and redirect back to frontend.
     */
    public function verify(Request $request, string $id, string $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            $frontendUrl = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173')), '/');
            return redirect("{$frontendUrl}/profile?verification=invalid");
        }

        if ($user->hasVerifiedEmail()) {
            $frontendUrl = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173')), '/');
            return redirect("{$frontendUrl}/profile?verification=already_verified");
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        $frontendUrl = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173')), '/');
        return redirect("{$frontendUrl}/profile?verification=success");
    }
}
