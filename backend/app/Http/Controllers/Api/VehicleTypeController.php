<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleType;

class VehicleTypeController extends Controller
{
    public function index()
    {
        $vehicleTypes = VehicleType::where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'slug',
                'icon',
            ]);

        return response()->json([
            'success' => true,
            'data' => $vehicleTypes,
        ]);
    }
}

