<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PhoneOtpService
{
    public const OTP_EXPIRY_SECONDS = 300; // 5 minutes
    public const RESEND_COOLDOWN_SECONDS = 60; // 60 seconds
    public const MAX_ATTEMPTS = 5;

    /**
     * Normalize Bangladesh and international phone numbers into E.164-like standard (+8801XXXXXXXXX).
     */
    public function normalizePhoneNumber(string $phone): string
    {
        // Strip out whitespace, dashes, parens
        $cleaned = preg_replace('/[\s\-\(\)]+/', '', $phone);

        // If local format 01XXXXXXXXX (11 digits), prepend +88
        if (preg_match('/^01[3-9]\d{8}$/', $cleaned)) {
            return '+88' . $cleaned;
        }

        // If 8801XXXXXXXXX (13 digits without plus), prepend +
        if (preg_match('/^8801[3-9]\d{8}$/', $cleaned)) {
            return '+' . $cleaned;
        }

        // If +8801XXXXXXXXX (14 characters), valid BD format
        if (preg_match('/^\+8801[3-9]\d{8}$/', $cleaned)) {
            return $cleaned;
        }

        // Other international format starting with +
        if (str_starts_with($cleaned, '+')) {
            return $cleaned;
        }

        return '+' . $cleaned;
    }

    /**
     * Generate and dispatch a 6-digit OTP for the given user and phone number.
     *
     * @return array{success: bool, message: string, cooldown_remaining?: int, dev_otp?: string}
     */
    public function sendOtp(User $user, string $phone): array
    {
        $normalizedPhone = $this->normalizePhoneNumber($phone);
        $cooldownKey = "phone_otp_cooldown_{$user->id}";
        $otpKey = "phone_otp_data_{$user->id}";

        if (Cache::has($cooldownKey)) {
            $remaining = Cache::get($cooldownKey) - time();
            if ($remaining > 0) {
                return [
                    'success' => false,
                    'message' => "Please wait {$remaining} seconds before requesting a new code.",
                    'cooldown_remaining' => $remaining,
                ];
            }
        }

        // Generate cryptographically secure 6-digit OTP
        $otp = (string) random_int(100000, 999999);

        // Store secure hash of OTP with expiration
        $otpData = [
            'phone' => $normalizedPhone,
            'otp_hash' => Hash::make($otp),
            'attempts' => 0,
            'expires_at' => time() + self::OTP_EXPIRY_SECONDS,
        ];

        Cache::put($otpKey, $otpData, self::OTP_EXPIRY_SECONDS);
        Cache::put($cooldownKey, time() + self::RESEND_COOLDOWN_SECONDS, self::RESEND_COOLDOWN_SECONDS);

        // Dispatch via SMS gateway (or Log driver for local/dev)
        $this->dispatchSms($normalizedPhone, "Your Parkly verification code is: {$otp}. It will expire in 5 minutes.");

        $response = [
            'success' => true,
            'message' => 'Verification code sent to your phone number.',
            'cooldown_remaining' => self::RESEND_COOLDOWN_SECONDS,
        ];

        // In local/testing environment, provide the OTP for verification inspection
        if (app()->environment('local', 'testing')) {
            $response['dev_otp'] = $otp;
        }

        return $response;
    }

    /**
     * Verify the user-supplied OTP.
     *
     * @return array{success: bool, message: string}
     */
    public function verifyOtp(User $user, string $code): array
    {
        $otpKey = "phone_otp_data_{$user->id}";

        if (!Cache::has($otpKey)) {
            return [
                'success' => false,
                'message' => 'Verification code has expired or was not requested. Please request a new code.',
            ];
        }

        $otpData = Cache::get($otpKey);

        if (time() > ($otpData['expires_at'] ?? 0)) {
            Cache::forget($otpKey);
            return [
                'success' => false,
                'message' => 'Verification code has expired. Please request a new one.',
            ];
        }

        if (($otpData['attempts'] ?? 0) >= self::MAX_ATTEMPTS) {
            Cache::forget($otpKey);
            return [
                'success' => false,
                'message' => 'Maximum verification attempts exceeded. Please request a new code.',
            ];
        }

        // Increment attempts
        $otpData['attempts'] = ($otpData['attempts'] ?? 0) + 1;
        Cache::put($otpKey, $otpData, max(1, ($otpData['expires_at'] - time())));

        // Validate hash
        if (!Hash::check($code, $otpData['otp_hash'])) {
            $remainingAttempts = self::MAX_ATTEMPTS - $otpData['attempts'];
            return [
                'success' => false,
                'message' => $remainingAttempts > 0
                    ? "Incorrect verification code. {$remainingAttempts} attempts remaining."
                    : 'Maximum verification attempts exceeded. Please request a new code.',
            ];
        }

        // Success - clean up OTP cache
        Cache::forget($otpKey);
        Cache::forget("phone_otp_cooldown_{$user->id}");

        // Update user's phone and verified timestamp
        $user->phone = $otpData['phone'];
        $user->phone_verified_at = now();
        $user->save();

        return [
            'success' => true,
            'message' => 'Phone number verified successfully.',
        ];
    }

    /**
     * Dispatch SMS message via configured provider or fallback to log.
     */
    protected function dispatchSms(string $phone, string $message): void
    {
        $smsDriver = config('services.sms.driver', env('SMS_DRIVER', 'log'));

        Log::info("SMS to {$phone}: {$message}");

        if ($smsDriver === 'log' || app()->environment('local', 'testing')) {
            return;
        }

        // Extensible SMS Gateway integration point (e.g., Twilio, SSL Wireless, Greenweb, etc.)
    }
}
