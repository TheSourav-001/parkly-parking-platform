<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PhoneOtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PhoneVerificationController extends Controller
{
    public function __construct(
        protected PhoneOtpService $otpService
    ) {}

    /**
     * Send OTP to the user's phone number.
     */
    public function sendOtp(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        // Phone can either be passed or taken from user model
        $request->validate([
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^([0-9\s\-\+\(\)]*)$/',
            ],
        ]);

        $phoneToVerify = $request->filled('phone') ? $request->input('phone') : $user->phone;

        if (empty($phoneToVerify)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please provide a valid phone number to verify.',
            ], 422);
        }

        $normalized = $this->otpService->normalizePhoneNumber($phoneToVerify);

        // Check uniqueness across other users
        $existing = User::where('phone', $normalized)
            ->where('id', '!=', $user->id)
            ->first();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'This phone number is already registered to another account.',
            ], 422);
        }

        // Check if current user already has this exact phone number verified
        if ($user->phone === $normalized && $user->phone_verified_at !== null) {
            return response()->json([
                'status' => 'success',
                'message' => 'Phone number is already verified.',
                'already_verified' => true,
            ], 200);
        }

        // Save normalized number to user model if unsaved
        if ($user->phone !== $normalized) {
            $user->phone = $normalized;
            $user->phone_verified_at = null;
            $user->save();
        }

        $result = $this->otpService->sendOtp($user, $normalized);

        if (!$result['success']) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'],
                'cooldown_remaining' => $result['cooldown_remaining'] ?? 0,
            ], 429);
        }

        $response = [
            'status' => 'success',
            'message' => $result['message'],
            'cooldown_remaining' => $result['cooldown_remaining'] ?? 60,
            'phone' => $normalized,
        ];

        if (isset($result['dev_otp'])) {
            $response['dev_otp'] = $result['dev_otp'];
        }

        return response()->json($response, 200);
    }

    /**
     * Verify the 6-digit OTP code submitted by the user.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'code' => ['required', 'string', 'digits:6'],
        ]);

        $result = $this->otpService->verifyOtp($user, $validated['code']);

        if (!$result['success']) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => $result['message'],
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'is_active' => (bool) $user->is_active,
                    'avatar' => $user->avatar,
                    'email_verified_at' => $user->email_verified_at?->toISOString(),
                    'phone_verified_at' => $user->phone_verified_at?->toISOString(),
                    'created_at' => $user->created_at?->toISOString(),
                    'updated_at' => $user->updated_at?->toISOString(),
                ],
            ],
        ], 200);
    }
}
