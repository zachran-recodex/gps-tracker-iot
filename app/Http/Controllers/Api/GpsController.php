<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GpsLocation;
use Illuminate\Http\Request;

class GpsController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Tangkap data mentah
            $content = $request->getContent();
            \Log::info('Received GPS data: ' . $content); // Untuk debugging

            // Parse JSON
            $data = json_decode($content, true);

            // Jika parsing JSON gagal, coba parse sebagai raw data
            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::warning('Failed to parse JSON, raw content: ' . $content);
                return response('OK', 200); // Tetap kirim OK untuk mencegah retry
            }

            // Simpan ke database
            $location = GpsLocation::create([
                'latitude' => $data['latitude'] ?? 0,
                'longitude' => $data['longitude'] ?? 0
            ]);

            // Kirim respons sederhana
            return response('OK', 200);

        } catch (\Exception $e) {
            \Log::error('GPS data error: ' . $e->getMessage());
            return response('Error', 500);
        }
    }
}
