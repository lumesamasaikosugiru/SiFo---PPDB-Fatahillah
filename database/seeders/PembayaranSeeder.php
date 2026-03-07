<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pembayarans')->insert([
            [
                'metode_pembayaran_id' => '1',
                'pendaftaran_id' => '1',
                'nominal' => '200000',
                'order_id' => 'order-20260001',
                'snap_token' => (string) Str::uuid(),
            ],
            [
                'metode_pembayaran_id' => '2',
                'pendaftaran_id' => '2',
                'nominal' => '200000',
                'order_id' => 'order-20260002',
                'snap_token' => (string) Str::uuid(),
            ],
            [
                'metode_pembayaran_id' => '1',
                'pendaftaran_id' => '3',
                'nominal' => '200000',
                'order_id' => 'order-20260003',
                'snap_token' => (string) Str::uuid(),
            ],
        ]);
    }
}
