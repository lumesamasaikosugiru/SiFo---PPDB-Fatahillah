<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WaliMuridSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('wali_siswas')->insert([
            [
                'pendaftaran_id' => '1',
                'nama_wali' => 'Yerami',
                'hubungan' => 'bapak',      // 'bapak' | 'ibu' | 'saudara_kandung' | 'saudara_keluarga'
                'alamat' => 'Jl. Langsat Tigapuluh1',
                'pekerjaan' => 'Pedagang UMKM',
                'notelp_wali' => '089812347890',
                'email' => 'yerami@gmail.dev',
            ],
            [
                'pendaftaran_id' => '2',
                'nama_wali' => 'Sumarni',
                'hubungan' => 'ibu',      // 'bapak' | 'ibu' | 'saudara_kandung' | 'saudara_keluarga'
                'alamat' => 'Jl. Makasaras Lembang',
                'pekerjaan' => 'Pedagang UMKM',
                'notelp_wali' => '089734347890',
                'email' => 'sumarni@gmail.dev',
            ],
            [
                'pendaftaran_id' => '3',
                'nama_wali' => 'Samanali',
                'hubungan' => 'bapak',      // 'bapak' | 'ibu' | 'saudara_kandung' | 'saudara_keluarga'
                'alamat' => 'Jl. Padang Sekali Ambar',
                'pekerjaan' => 'Wirausaha',
                'notelp_wali' => '081712347899',
                'email' => 'samanali@gmail.dev',
            ],
        ]);
    }
}
