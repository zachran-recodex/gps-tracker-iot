<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GpsController;

Route::post('/gps', [GpsController::class, 'store']);
