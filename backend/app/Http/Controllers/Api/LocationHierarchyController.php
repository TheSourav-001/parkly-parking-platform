<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationHierarchyController extends Controller
{
    /**
     * GET /api/locations/districts
     * Dhaka-only district list (Dhaka District)
     */
    public function districts(): JsonResponse
    {
        $districts = Location::where('type', 'district')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('slug', 'like', '%dhaka%')
                  ->orWhere('name', 'like', '%Dhaka%');
            })
            ->orderBy('name_en')
            ->get()
            ->map(function ($loc) {
                $facilityCount = $loc->parkingFacilities()->where('is_active', true)->count();
                return [
                    'id' => $loc->id,
                    'name' => $loc->name,
                    'name_en' => $loc->name_en ?? $loc->name,
                    'name_bn' => $loc->name_bn ?? $loc->name,
                    'slug' => $loc->slug,
                    'facility_count' => $facilityCount,
                    'has_facilities' => $facilityCount > 0,
                ];
            });

        return response()->json(['data' => $districts]);
    }

    /**
     * GET /api/locations/thanas?district_id=
     */
    public function thanas(Request $request): JsonResponse
    {
        $districtId = $request->query('district_id');
        $query = Location::where('type', 'thana')->where('is_active', true);

        if ($districtId) {
            $query->where('parent_id', $districtId);
        }

        $thanas = $query->orderBy('name_en')->get()->map(function ($loc) {
            $facilityCount = $loc->parkingFacilities()->where('is_active', true)->count();
            return [
                'id' => $loc->id,
                'district_id' => $loc->parent_id,
                'name' => $loc->name,
                'name_en' => $loc->name_en ?? $loc->name,
                'name_bn' => $loc->name_bn ?? $loc->name,
                'slug' => $loc->slug,
                'latitude' => (float)$loc->latitude,
                'longitude' => (float)$loc->longitude,
                'facility_count' => $facilityCount,
                'has_facilities' => $facilityCount > 0,
            ];
        });

        return response()->json(['data' => $thanas]);
    }

    /**
     * GET /api/locations/areas?thana_id=
     */
    public function areas(Request $request): JsonResponse
    {
        $thanaId = $request->query('thana_id');
        $query = Location::where('type', 'area')->where('is_active', true);

        if ($thanaId) {
            $query->where('parent_id', $thanaId);
        }

        $areas = $query->orderBy('name_en')->get()->map(function ($loc) {
            $facilityCount = $loc->parkingFacilities()->where('is_active', true)->count();
            return [
                'id' => $loc->id,
                'thana_id' => $loc->parent_id,
                'name' => $loc->name,
                'name_en' => $loc->name_en ?? $loc->name,
                'name_bn' => $loc->name_bn ?? $loc->name,
                'slug' => $loc->slug,
                'latitude' => (float)$loc->latitude,
                'longitude' => (float)$loc->longitude,
                'facility_count' => $facilityCount,
                'has_facilities' => $facilityCount > 0,
            ];
        });

        return response()->json(['data' => $areas]);
    }

    /**
     * GET /api/locations/coverage
     * Dhaka coverage cards with facility counts
     */
    public function coverage(): JsonResponse
    {
        $thanas = Location::where('type', 'thana')
            ->where('is_active', true)
            ->withCount(['parkingFacilities' => function ($q) {
                $q->where('is_active', true);
            }])
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'name' => $t->name_en ?? $t->name,
                    'name_bn' => $t->name_bn ?? $t->name,
                    'slug' => $t->slug,
                    'facility_count' => $t->parking_facilities_count,
                    'latitude' => (float)$t->latitude,
                    'longitude' => (float)$t->longitude,
                    'status' => $t->parking_facilities_count > 0 ? 'active' : 'coming_soon',
                ];
            });

        return response()->json([
            'city' => 'Dhaka',
            'areas' => $thanas,
            'note' => 'Dhaka first, more cities later.',
        ]);
    }
}
