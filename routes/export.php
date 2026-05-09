<?php

// Tambahkan baris ini ke routes/web.php:
// require __DIR__.'/export.php';

use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'web'])->prefix('admin/export')->name('admin.export.')->group(function () {
    Route::get('/pendaftaran/pdf',   [ExportController::class, 'pendaftaranPdf'])   ->name('pendaftaran.pdf');
    Route::get('/pendaftaran/excel', [ExportController::class, 'pendaftaranExcel']) ->name('pendaftaran.excel');
    Route::get('/pembayaran/pdf',    [ExportController::class, 'pembayaranPdf'])    ->name('pembayaran.pdf');
    Route::get('/pembayaran/excel',  [ExportController::class, 'pembayaranExcel'])  ->name('pembayaran.excel');
    // ── Laporan Rekap
    Route::get('/laporan/pdf',       [ExportController::class, 'laporanPdf'])       ->name('laporan.pdf');
    Route::get('/laporan/excel',     [ExportController::class, 'laporanExcel'])     ->name('laporan.excel');
});
