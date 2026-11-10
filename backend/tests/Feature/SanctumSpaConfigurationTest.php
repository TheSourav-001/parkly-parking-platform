<?php

namespace Tests\Feature;

use Tests\TestCase;

class SanctumSpaConfigurationTest extends TestCase
{
    public function test_csrf_cookie_endpoint_is_accessible_and_sets_cookie(): void
    {
        $response = $this->withHeaders([
            'Origin' => 'http://localhost:5173',
            'Referer' => 'http://localhost:5173/',
        ])->get('/sanctum/csrf-cookie');

        $response->assertNoContent();
        $response->assertCookie('XSRF-TOKEN');
        $response->assertHeader('Access-Control-Allow-Origin', 'http://localhost:5173');
        $response->assertHeader('Access-Control-Allow-Credentials', 'true');
    }

    public function test_cors_allows_credentials_for_first_party_origin(): void
    {
        $response = $this->withHeaders([
            'Origin' => 'http://localhost:5173',
            'Access-Control-Request-Method' => 'POST',
            'Access-Control-Request-Headers' => 'X-XSRF-TOKEN, Content-Type, Accept',
        ])->optionsJson('/api/health-check');

        $response->assertStatus(204);
        $response->assertHeader('Access-Control-Allow-Origin', 'http://localhost:5173');
        $response->assertHeader('Access-Control-Allow-Credentials', 'true');
    }

    public function test_cors_rejects_unauthorized_third_party_origin(): void
    {
        $response = $this->withHeaders([
            'Origin' => 'http://evil-site.com',
            'Access-Control-Request-Method' => 'GET',
        ])->optionsJson('/api/health-check');

        // Should not reflect untrusted origins
        $this->assertNotEquals('http://evil-site.com', $response->headers->get('Access-Control-Allow-Origin'));
    }

    public function test_api_health_check_endpoint(): void
    {
        $response = $this->withHeaders([
            'Origin' => 'http://localhost:5173',
            'Accept' => 'application/json',
        ])->getJson('/api/health-check');

        $response->assertOk();
        $response->assertJson([
            'status' => 'healthy',
            'platform' => 'Parkly API',
        ]);
        $response->assertHeader('Access-Control-Allow-Origin', 'http://localhost:5173');
        $response->assertHeader('Access-Control-Allow-Credentials', 'true');
    }
}

