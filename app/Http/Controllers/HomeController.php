<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil semua sekolah dengan jurusan aktif
        $sekolahGroup = Sekolah::with(['jurusans' => function ($q) {
            $q->where('is_active', 1);
        }])->orderBy('nama_sekolah')->get()->groupBy('tingkatan');

        // Hitung jurusan aktif untuk hero stat
        $jumlahJurusan = Jurusan::where('is_active', 1)->count();

        return view('home', compact('sekolahGroup', 'jumlahJurusan'));
    }
}
