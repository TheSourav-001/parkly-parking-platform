<?php

namespace Tests\Feature;

use App\Models\VehicleType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PricingRangeApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_pricing_ranges_endpoint_returns_success_structure(): void
    {
        $response = $this->getJson('/api/pricing/ranges');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'min_hourly_rate',
                        'max_hourly_rate',
                        'rate_display',
                        'currency',
                        'facility_count',
                        'is_published',
                    ],
                ],
                'meta' => [
                    'currency',
                    'source',
                    'notice',
                ],
            ]);

        $this->assertTrue($response->json('success'));
    }

    public function test_pricing_ranges_has_honest_rate_display(): void
    {
        $response = $this->getJson('/api/pricing/ranges');
        $response->assertStatus(200);

        $data = $response->json('data');

        foreach ($data as $item) {
            if ($item['min_hourly_rate'] === null || $item['max_hourly_rate'] === null) {
                $this->assertEquals('Rates not published yet', $item['rate_display']);
                $this->assertFalse($item['is_published']);
            } else {
                $this->assertStringContainsString('BDT', $item['rate_display']);
            }
        }
    }
}

