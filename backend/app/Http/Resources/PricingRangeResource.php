<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PricingRangeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description ?? $this->defaultDescription($this->slug),
            'min_hourly_rate' => $this->min_hourly_rate !== null ? (float)$this->min_hourly_rate : null,
            'max_hourly_rate' => $this->max_hourly_rate !== null ? (float)$this->max_hourly_rate : null,
            'rate_display' => $this->formatRateDisplay($this->min_hourly_rate, $this->max_hourly_rate),
            'currency' => 'BDT',
            'facility_count' => (int)($this->facility_count ?? 0),
            'is_published' => $this->facility_count > 0 && $this->min_hourly_rate !== null,
        ];
    }

    private function defaultDescription(string $slug): string
    {
        return match ($slug) {
            'car' => 'Standard cars, sedans and compact hatchbacks.',
            'suv' => 'SUVs, crossovers, 4x4s and full-sized passenger vehicles.',
            'motorcycle' => 'Standard motorbikes, scooters and two-wheelers.',
            'others', 'microbus', 'truck' => 'Vans, microbuses and light commercial vehicles.',
            default => 'Standard vehicle parking slots.'
        };
    }

    private function formatRateDisplay($min, $max): string
    {
        if ($min === null || $max === null) {
            return 'Rates not published yet';
        }
        if ((float)$min === (float)$max) {
            return (int)$min . ' BDT per hour';
        }
        return (int)$min . ' to ' . (int)$max . ' BDT per hour';
    }
}

