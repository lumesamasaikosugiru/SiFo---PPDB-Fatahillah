<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SekolahSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama_sekolah' => 'SMK YP Fatahillah 1 Kramatwatu',
                'tingkatan' => 'SMK',
                'alamat' => 'Jl. Gsi, Kramatwatu',
                'kuota' => '100',
            ],
            [
                'nama_sekolah' => 'SMK YP Fatahillah 1 Cilegon',
                'tingkatan' => 'SMK',
                'alamat' => 'Jl. Kubang Sepat, Cilegon',
                'kuota' => '190',
            ],
            [
                'nama_sekolah' => 'SMK YP Fatahillah 2 Cilegon',
                'tingkatan' => 'SMK',
                'alamat' => 'Jl.Kubang Sepat, Cilegon',
                'kuota' => '180',
            ],
            [
                'nama_sekolah' => 'SMP YP Fatahillah Cilegon',
                'tingkatan' => 'SMP',
                'alamat' => 'Jl.Kubang Sepat, Cilegon',
                'kuota' => '90',
            ],
        ];
        DB::table('sekolahs')->truncate();
        foreach ($data as $item) {
            DB::table('sekolahs')->insert($item);
        }
    }
}