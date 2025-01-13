<?php

namespace App\Http\Controllers;

use App\Models\GpsLocation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Hapus data lama jika jumlah data melebihi 20
        $this->deleteOldLocations();

        // Ambil 5 data terbaru
        $lastLocation = GpsLocation::latest()->first();
        $locations = GpsLocation::latest()->take(5)->get();

        return view('dashboard', compact('lastLocation', 'locations'));
    }

    public function getLocations()
    {
        // Ambil 5 data terbaru
        $locations = GpsLocation::latest()->take(5)->get();
        return response()->json($locations);
    }

    private function deleteOldLocations()
    {
        // Hitung total data
        $totalLocations = GpsLocation::count();

        // Jika data lebih dari 20, hapus data lama
        if ($totalLocations > 20) {
            $locationsToDelete = GpsLocation::orderBy('created_at', 'asc')
                ->take($totalLocations - 20)
                ->pluck('id');

            GpsLocation::whereIn('id', $locationsToDelete)->delete();
        }
    }
}
