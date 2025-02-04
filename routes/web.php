<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogViewerController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/locations', [DashboardController::class, 'getLocations'])->name('locations.get');

    Route::get('/view-log', [LogViewerController::class, 'index'])->name('logs.index');
    Route::get('/view-log/download', [LogViewerController::class, 'download'])->name('logs.download');
    Route::post('/view-log/clear', [LogViewerController::class, 'clear'])->name('logs.clear');
});

Route::domain('ta.' . env('APP_URL'))->group(function () {
    Route::get('/', function () {
        return 'Ini adalah subdomain ta.smartracker.web.id';
    });

});

require __DIR__.'/auth.php';
