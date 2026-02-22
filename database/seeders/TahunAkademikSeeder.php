<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TahunAkademikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tahun_akademiks')->insert([
            [
                'tahun' => '2023/2024',
            ],
            [
                'tahun' => '2024/2025',
            ],
            [
                'tahun' => '2025/2026',
            ],
            [
                'tahun' => '2026/2027',
            ],
        ]);
    }
}
