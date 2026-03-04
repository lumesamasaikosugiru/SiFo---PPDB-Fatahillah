<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PendaftaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pendaftarans')->insert([
            [
                'kode_regis' => 'PPDB26-GGTD3SVP',
                'tahun_akademik_id' => '4',
                'sekolah_id' => '1',
                'jurusan_id' => '1',
                'jalur_pendaftaran' => 'reguler',
                'status' => 'diproses',
                'tanggal_submit' => '2026-03-03',
                'dibuat_oleh' => 'publik',
            ],
            [
                'kode_regis' => 'PPDB26-GGTD3SVO',
                'tahun_akademik_id' => '4',
                'sekolah_id' => '2',
                'jurusan_id' => '1',
                'jalur_pendaftaran' => 'reguler',
                'status' => 'diproses',
                'tanggal_submit' => '2026-03-03',
                'dibuat_oleh' => 'publik',
            ],
            [
                'kode_regis' => 'PPDB26-GGTD3SVI',
                'tahun_akademik_id' => '4',
                'sekolah_id' => '3',
                'jurusan_id' => '9',
                'jalur_pendaftaran' => 'reguler',
                'status' => 'diproses',
                'tanggal_submit' => '2026-03-03',
                'dibuat_oleh' => 'publik',
            ],
        ]);
    }
}
