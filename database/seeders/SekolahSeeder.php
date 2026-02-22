<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sekolahs')->insert([
            [
                'nama_sekolah' => 'SMK YPF 1 Kramatwatu',
                'tingkatan' => 'SMK',
                'alamat' => 'Jl. Gsi, Kramatwatu',
                'kuota' => '100',
            ],
            [
                'nama_sekolah' => 'SMK YPF 1 Cilegon',
                'tingkatan' => 'SMK',
                'alamat' => 'Jl. Kubang Sepat, Cilegon',
                'kuota' => '190',
            ],
            [
                'nama_sekolah' => 'SMK YPF 2 Cilegon',
                'tingkatan' => 'SMK',
                'alamat' => 'Jl.Kubang Sepat, Cilegon',
                'kuota' => '180',
            ],
            [
                'nama_sekolah' => 'SMP YPF Cilegon',
                'tingkatan' => 'SMP',
                'alamat' => 'Jl.Kubang Sepat, Cilegon',
                'kuota' => '90',
            ],
        ]);
    }
}
