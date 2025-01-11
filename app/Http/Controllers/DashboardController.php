<?php

namespace App\Http\Controllers;

use App\Models\GpsLocation;

class DashboardController extends Controller
{
    public function index()
    {
        $lastLocation = GpsLocation::latest()->first();
        $locations = GpsLocation::latest()->take(10)->get();

        return view('dashboard', compact('lastLocation', 'locations'));
    }

    public function getLocations()
    {
        $locations = GpsLocation::latest()->take(100)->get();
        return response()->json($locations);
    }
}