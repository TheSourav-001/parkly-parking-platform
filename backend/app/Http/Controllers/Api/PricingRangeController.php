<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PricingRangeResource;
use App\Models\VehicleType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PricingRangeController extends Controller
{
    /**
     * GET /api/pricing/ranges
     *
     * Returns active vehicle types aggregated with min & max hourly rates
     * and facility counts strictly across active, registered parking facilities.
     */
    public function index(): JsonResponse
    {
        // Query active vehicle types
        $vehicleTypes = VehicleType::where('is_active', true)
            ->whereIn('slug', ['car', 'suv', 'motorcycle', 'others', 'microbus'])
            ->orderByRaw("CASE 
                WHEN slug = 'car' THEN 1
                WHEN slug = 'suv' THEN 2
                WHEN slug = 'motorcycle' THEN 3
                ELSE 4
            END")
            ->get();

        // If 'others' doesn't exist but 'microbus' exists, we alias it or normalize
        $results = [];
        $hasOthers = false;

        foreach ($vehicleTypes as $type) {
            // Aggregate from facility_vehicle_rates joined with active parking_facilities
            $stats = DB::table('facility_vehicle_rates')
                ->join('parking_facilities', 'facility_vehicle_rates.parking_facility_id', '=', 'parking_facilities.id')
                ->where('parking_facilities.is_active', true)
                ->where('facility_vehicle_rates.is_active', true)
                ->where('facility_vehicle_rates.vehicle_type_id', $type->id)
                ->select(
                    DB::raw('MIN(facility_vehicle_rates.rate_amount) as min_rate'),
                    DB::raw('MAX(facility_vehicle_rates.rate_amount) as max_rate'),
                    DB::raw('COUNT(DISTINCT parking_facilities.id) as facility_count')
                )
                ->first();

            $type->min_hourly_rate = $stats && $stats->min_rate !== null ? (float)$stats->min_rate : null;
            $type->max_hourly_rate = $stats && $stats->max_rate !== null ? (float)$stats->max_rate : null;
            $type->facility_count = $stats ? (int)$stats->facility_count : 0;

            if ($type->slug === 'microbus' || $type->slug === 'truck') {
                if ($hasOthers) continue;
                $type->slug = 'others';
                $type->name = 'Others / Microbus';
                $hasOthers = true;
            } elseif ($type->slug === 'others') {
                $hasOthers = true;
            }

            $results[] = new PricingRangeResource($type);
        }

        return response()->json([
            'success' => true,
            'data' => $results,
            'meta' => [
                'currency' => 'BDT',
                'source' => 'Verified Parkly database records',
                'notice' => 'Operator-reported rates across registered Dhaka facilities.',
            ],
        ]);
    }
}

