<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index()
    {
        return view('status');
    }

    public function check(Request $request)
    {
        $request->validate([
            'kode_registrasi' => 'required|string|max:20',
        ], [
            'kode_registrasi.required' => 'Nomor pendaftaran wajib diisi.',
        ]);

        $pendaftaran = Pendaftaran::with([
            'siswa',
            'jurusan',
            'sekolah',
            'waliSiswas',
            'dokumens',
        ])->where('kode_regis', strtoupper(trim($request->kode_registrasi)))
          ->first();

        if (! $pendaftaran) {
            return back()->withErrors([
                'kode_registrasi' => 'Nomor pendaftaran tidak ditemukan. Pastikan nomor yang dimasukkan benar.',
            ])->withInput();
        }

        return view('status', compact('pendaftaran'));
    }
}
