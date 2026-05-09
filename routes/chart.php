<?php

use App\Http\Controllers\ChartController;
use Illuminate\Support\Facades\Route;

// Chart iframe & API — wajib login (auth middleware)
Route::middleware(['auth', 'web'])->group(function () {
    Route::get('/admin/chart',      [ChartController::class, 'iframe'])->name('chart.iframe');
    Route::get('/admin/chart/data', [ChartController::class, 'data'])->name('chart.data');
});
