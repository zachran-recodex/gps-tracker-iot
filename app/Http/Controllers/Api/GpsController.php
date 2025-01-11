<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GpsLocation;
use Illuminate\Http\Request;

class GpsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $location = GpsLocation::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Location saved successfully'
        ]);
    }
}
