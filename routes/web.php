<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\PembayaranController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/daftar', [PendaftaranController::class, 'create'])->name('daftar.create');
Route::post('/daftar', [PendaftaranController::class, 'store'])->name('daftar.store');
Route::get('/pendaftaran/selesai', [PendaftaranController::class, 'finish'])->name('pendaftaran.finish');

Route::get('/cek-status', [StatusController::class, 'index'])->name('status.index');
Route::post('/cek-status', [StatusController::class, 'check'])->name('status.check');

// --- Pembayaran ---
// --- Pembayaran ---
// Route::get('/bayar', [PembayaranController::class, 'index'])->name('pembayaran.index');
// Route::post('/bayar/cek', [PembayaranController::class, 'cek'])->name('pembayaran.cek');
// Route::get('/bayar/cek', fn() => redirect()->route('pembayaran.index')); // GET fallback - cegah 405
// Route::post('/bayar/store', [PembayaranController::class, 'store'])->name('pembayaran.store');
// Route::get('/bayar/status', [PembayaranController::class, 'status'])->name('pembayaran.status');
// Route::get('/bayar/cek-status', function () {
//     return view('pembayaran.cek-status');
// })->name('pembayaran.status.cek');

// // ===== PEMBAYARAN =====
// Route::prefix('bayar')->name('pembayaran.')->group(function () {

//     // Halaman input kode (existing)
//     Route::get('/',                     [PembayaranController::class, 'index'])->name('index');

//     // Cek kode & form bayar (existing)
//     Route::get('/cek',                  [PembayaranController::class, 'cek'])->name('cek');
//     Route::post('/cek',                 [PembayaranController::class, 'cek'])->name('cek.post');

//     // Submit pembayaran manual (existing)
//     Route::post('/store',               [PembayaranController::class, 'store'])->name('store');

//     // Status pembayaran (existing)
//     Route::get('/status',               [PembayaranController::class, 'status'])->name('status');
//     Route::get('/status/cek',           [StatusController::class, 'index'])->name('status.cek'); // jika ada

//     // ===== MIDTRANS — BARU =====

//     // AJAX: request snap token
//     Route::post('/request-snap-token',  [PembayaranController::class, 'requestSnapToken'])->name('requestSnapToken');

//     // Halaman tampil ulang snap popup (untuk yang masih pending)
//     Route::get('/snap/{snapToken}',     [PembayaranController::class, 'snapPage'])->name('snapPage');

//     // Redirect dari restapi.riplabs.co.id setelah finish/error payment
//     Route::get('/informasi',            [PembayaranController::class, 'informasi'])->name('informasi');

//     // Callback dari restapi.riplabs.co.id (dipanggil backend riplabs)
//     Route::post('/onprogressmidtrans',  [PembayaranController::class, 'onProgressMidtrans'])->name('onprogressmidtrans');
// });

// =====================================================
// ROUTES PEMBAYARAN PPDB
// Tambahkan semua baris ini ke routes/web.php
// =====================================================

// --- Halaman utama input kode ---
Route::get('/bayar', [PembayaranController::class, 'index'])->name('pembayaran.index');

// --- Cek kode & tampilkan form (POST only) ---
Route::post('/bayar/cek', [PembayaranController::class, 'cek'])->name('pembayaran.cek');
// GET fallback: cegah 405 jika user akses langsung via browser URL
Route::get('/bayar/cek', fn() => redirect()->route('pembayaran.index'))->name('pembayaran.cek.get');

// --- Submit pembayaran manual (transfer/cash) ---
Route::post('/bayar/store', [PembayaranController::class, 'store'])->name('pembayaran.store');

// --- Halaman status pembayaran ---
Route::get('/bayar/status', [PembayaranController::class, 'status'])->name('pembayaran.status');

// --- Halaman cek status (input kode) ---
Route::get('/bayar/cek-status', fn() => view('pembayaran.cek-status'))->name('pembayaran.status.cek');

// =====================================================
// ROUTES MIDTRANS
// =====================================================

// Request snap token (AJAX POST dari form)
Route::post('/bayar/snap-token', [PembayaranController::class, 'requestSnapToken'])->name('pembayaran.requestSnapToken');

// Halaman snap (lanjutkan pembayaran pending)
Route::get('/bayar/snap/{snapToken}', [PembayaranController::class, 'snapPage'])->name('pembayaran.snapPage');

// Halaman lanjutkan Midtrans pending
Route::get('/bayar/lanjut', [PembayaranController::class, 'snapLanjut'])->name('pembayaran.snapLanjut');

// Reset snap token (batalkan pending, kembali ke form)
Route::post('/bayar/reset-snap', [PembayaranController::class, 'resetSnap'])->name('pembayaran.resetSnap');
Route::get('/bayar/reset-snap', fn() => redirect()->route('pembayaran.index'));

// Halaman informasi setelah redirect dari Midtrans/riplabs
Route::get('/bayar/informasi', [PembayaranController::class, 'informasi'])->name('pembayaran.informasi');

// Halaman SUKSES pembayaran Midtrans (setelah onSuccess)
Route::get('/bayar/sukses', [PembayaranController::class, 'sukses'])->name('pembayaran.sukses');

// Download PDF Formulir Pendaftaran (hanya jika sudah lunas)
Route::get('/bayar/download-pdf', [PembayaranController::class, 'downloadPdf'])->name('pembayaran.downloadPdf');

// Callback dari riplabs (webhook) — CSRF exempt
Route::post('/bayar/onprogressmidtrans', [PembayaranController::class, 'onProgressMidtrans'])
    ->name('pembayaran.midtransCallback')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Fallback: update status sukses dari JS onSuccess (jika callback riplabs lambat/gagal)
Route::post('/bayar/payment-success', [PembayaranController::class, 'handlePaymentSuccess'])
    ->name('pembayaran.paymentSuccess');

// Callback dari riplabs (webhook) — CSRF exempt, tambahkan ke VerifyCsrfToken $except
Route::post('/bayar/onprogressmidtrans', [PembayaranController::class, 'onProgressMidtrans'])
    ->name('pembayaran.midtransCallback')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);