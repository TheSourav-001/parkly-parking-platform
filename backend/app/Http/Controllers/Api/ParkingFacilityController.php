<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ParkingFacility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParkingFacilityController extends Controller
{
    /**
     * GET /api/v1/facilities
     *
     * Returns all active parking facilities with location, vehicle rates, and availability.
     * Supports optional filters: lat, lng, radius (km), vehicle_type (slug), q (text search).
     */
    public function index(Request $request): JsonResponse
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $radius = $request->query('radius', 10);
        $vehicleType = $request->query('vehicle_type');
        $search = $request->query('q');

        $query = ParkingFacility::query()
            ->where('is_active', true)
            ->with(['location', 'vehicleRates.vehicleType', 'availabilities.vehicleType']);

        // If lat/lng provided, use Haversine formula to calculate distance and filter by radius
        if ($lat !== null && $lng !== null) {
            $lat = (float) $lat;
            $lng = (float) $lng;
            $radius = (float) $radius;

            // Haversine formula: distance in km
            $haversine = "(
                6371 * acos(
                    cos(radians(?))
                    * cos(radians(latitude))
                    * cos(radians(longitude) - radians(?))
                    + sin(radians(?))
                    * sin(radians(latitude))
                )
            )";

            $query->selectRaw("parking_facilities.*, {$haversine} AS distance", [$lat, $lng, $lat])
                ->having('distance', '<=', $radius)
                ->orderBy('distance', 'asc');
        } else {
            $query->select('parking_facilities.*')
                ->orderBy('name', 'asc');
        }

        // Filter by vehicle type slug
        if ($vehicleType) {
            $query->whereHas('vehicleRates', function ($q) use ($vehicleType) {
                $q->whereHas('vehicleType', function ($q2) use ($vehicleType) {
                    $q2->where('slug', $vehicleType);
                });
            });
        }

        // Text search on name and address
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address_text', 'like', "%{$search}%");
            });
        }

        $facilities = $query->get();

        return response()->json([
            'data' => $facilities,
            'meta' => [
                'total' => $facilities->count(),
            ],
        ]);
    }

    /**
     * GET /api/v1/facilities/{id}
     *
     * Returns a single facility with all relations.
     */
    public function show($id): JsonResponse
    {
        $facility = ParkingFacility::with([
            'location',
            'vehicleRates.vehicleType',
            'availabilities.vehicleType',
        ])->findOrFail($id);

        return response()->json([
            'data' => $facility,
        ]);
    }
}
