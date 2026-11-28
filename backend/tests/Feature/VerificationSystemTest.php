<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\PhoneOtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;
use Tests\TestCase;

class VerificationSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_requests_are_rejected(): void
    {
        $this->postJson('/api/v1/auth/phone/send-otp', ['phone' => '+8801700000000'])
            ->assertStatus(401);

        $this->postJson('/api/v1/auth/phone/verify-otp', ['code' => '123456'])
            ->assertStatus(401);

        $this->postJson('/api/v1/auth/email/verification-notification')
            ->assertStatus(401);
    }

    public function test_send_phone_otp_requires_phone(): void
    {
        $user = User::factory()->create(['phone' => null]);

        $this->actingAs($user)
            ->postJson('/api/v1/auth/phone/send-otp')
            ->assertStatus(422)
            ->assertJsonPath('status', 'error');
    }

    public function test_send_and_verify_phone_otp_successfully(): void
    {
        $user = User::factory()->create([
            'phone' => '01712345678',
            'phone_verified_at' => null,
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/v1/auth/phone/send-otp');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $devOtp = $response->json('dev_otp');
        $this->assertNotNull($devOtp);

        // Verify with invalid OTP first
        $this->actingAs($user)
            ->postJson('/api/v1/auth/phone/verify-otp', ['code' => '000000'])
            ->assertStatus(422);

        // Verify with correct OTP
        $verifyRes = $this->actingAs($user)
            ->postJson('/api/v1/auth/phone/verify-otp', ['code' => $devOtp]);

        $verifyRes->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $user->refresh();
        $this->assertNotNull($user->phone_verified_at);
        $this->assertEquals('+8801712345678', $user->phone);
    }

    public function test_updating_phone_number_in_profile_invalidates_phone_verified_at(): void
    {
        $user = User::factory()->create([
            'phone' => '+8801712345678',
            'phone_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->putJson('/api/v1/auth/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '+8801799999999',
            ])
            ->assertStatus(200);

        $user->refresh();
        $this->assertNull($user->phone_verified_at);
        $this->assertEquals('+8801799999999', $user->phone);
    }

    public function test_send_email_verification_notification_dispatches_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user)
            ->postJson('/api/v1/auth/email/verification-notification')
            ->assertStatus(200)
            ->assertJsonPath('status', 'success');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_email_verification_via_signed_url_works(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->get($verificationUrl);

        $response->assertRedirect('http://localhost:5173/profile?verification=success');

        $user->refresh();
        $this->assertTrue($user->hasVerifiedEmail());
    }

    public function test_email_verification_with_invalid_signature_is_handled(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $invalidUrl = "/email/verify/{$user->id}/invalid-hash?expires=" . (time() + 3600) . "&signature=invalid";

        $this->get($invalidUrl)
            ->assertStatus(403);
    }
}
