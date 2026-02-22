<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodePembayaran extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('metode_pembayarans')->insert([
            [
                'nama_metode' => 'otomatis',
                'deskripsi' => 'Midtrans',
                'is_active' => '1',
            ],
            [
                'nama_metode' => 'transfer',
                'deskripsi' => 'BRI - 22006866',
                'is_active' => '1',
            ],
            [
                'nama_metode' => 'cash',
                'deskripsi' => 'null',
                'is_active' => '1',
            ],
        ]);
    }
}
