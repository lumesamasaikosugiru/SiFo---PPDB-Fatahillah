<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StatusController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/daftar', [PendaftaranController::class, 'create'])->name('daftar.create');
Route::post('/daftar', [PendaftaranController::class, 'store'])->name('daftar.store');
Route::get('/pendaftaran/selesai', [PendaftaranController::class, 'finish'])->name('pendaftaran.finish');

Route::get('/cek-status', [StatusController::class, 'index'])->name('status.index');
Route::post('/cek-status', [StatusController::class, 'check'])->name('status.check');