<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:division,district,thana',
            'parent_id' => 'nullable|integer|exists:locations,id',
        ]);

        $type = $validated['type'];
        $parentId = $validated['parent_id'] ?? null;

        if ($type === 'division') {
            $locations = Location::where('type', 'division')
                ->orderBy('name')
                ->get(['id', 'parent_id', 'name', 'type', 'slug']);

            return response()->json([
                'success' => true,
                'data' => $locations,
            ]);
        }

        $expectedParentType = $type === 'district'
            ? 'division'
            : 'district';

        if (!$parentId) {
            return response()->json([
                'success' => false,
                'message' => 'A parent_id is required for this location type.',
            ], 422);
        }

        $parent = Location::where('id', $parentId)
            ->where('type', $expectedParentType)
            ->first();

        if (!$parent) {
            return response()->json([
                'success' => false,
                'message' => 'The selected parent location is invalid.',
            ], 422);
        }

        $locations = Location::where('parent_id', $parentId)
            ->where('type', $type)
            ->orderBy('name')
            ->get(['id', 'parent_id', 'name', 'type', 'slug']);

        return response()->json([
            'success' => true,
            'data' => $locations,
        ]);
    }
}
