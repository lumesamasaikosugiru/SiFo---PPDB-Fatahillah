<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PembayaranSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('pembayarans')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = Carbon::now();

        DB::table('pembayarans')->insert(['pendaftaran_id'=>5,'metode_pembayaran_id'=>1,'nominal'=>200000,'status_pembayaran'=>'menunggu_verifikasi','order_id'=>'PPDBATIKA-AAA55555-001','snap_token'=>'dummy-snap-token-aaa55555','proof_path'=>null,'tanggal_pembayaran'=>$now->copy()->subDays(5)->toDateString(),'catatan'=>null,'verifikasi_oleh'=>null,'verifikasi_tanggal'=>null,'created_at'=>$now->copy()->subDays(5),'updated_at'=>$now->copy()->subDays(5)]);
        DB::table('pembayarans')->insert(['pendaftaran_id'=>6,'metode_pembayaran_id'=>2,'nominal'=>200000,'status_pembayaran'=>'menunggu_verifikasi','order_id'=>null,'snap_token'=>null,'proof_path'=>null,'tanggal_pembayaran'=>$now->copy()->subDays(4)->toDateString(),'catatan'=>null,'verifikasi_oleh'=>null,'verifikasi_tanggal'=>null,'created_at'=>$now->copy()->subDays(4),'updated_at'=>$now->copy()->subDays(4)]);
        DB::table('pembayarans')->insert(['pendaftaran_id'=>7,'metode_pembayaran_id'=>2,'nominal'=>200000,'status_pembayaran'=>'sukses','order_id'=>null,'snap_token'=>null,'proof_path'=>null,'tanggal_pembayaran'=>$now->copy()->subDays(4)->toDateString(),'catatan'=>'Transfer BRI sudah masuk, dikonfirmasi.','verifikasi_oleh'=>1,'verifikasi_tanggal'=>$now->copy()->subDays(3),'created_at'=>$now->copy()->subDays(5),'updated_at'=>$now->copy()->subDays(3)]);
        DB::table('pembayarans')->insert(['pendaftaran_id'=>8,'metode_pembayaran_id'=>1,'nominal'=>200000,'status_pembayaran'=>'sukses','order_id'=>'PPDBATIKA-BBB33333-001','snap_token'=>null,'proof_path'=>null,'tanggal_pembayaran'=>$now->copy()->subDays(6)->toDateString(),'catatan'=>'Pembayaran via GoPay - otomatis dikonfirmasi Midtrans.','verifikasi_oleh'=>null,'verifikasi_tanggal'=>$now->copy()->subDays(6),'created_at'=>$now->copy()->subDays(6),'updated_at'=>$now->copy()->subDays(6)]);
        DB::table('pembayarans')->insert(['pendaftaran_id'=>12,'metode_pembayaran_id'=>1,'nominal'=>200000,'status_pembayaran'=>'menunggu_verifikasi','order_id'=>'PPDBATIKA-CCC22222-001','snap_token'=>'dummy-snap-token-ccc22222','proof_path'=>null,'tanggal_pembayaran'=>$now->copy()->subDays(2)->toDateString(),'catatan'=>null,'verifikasi_oleh'=>null,'verifikasi_tanggal'=>null,'created_at'=>$now->copy()->subDays(2),'updated_at'=>$now->copy()->subDays(2)]);
        DB::table('pembayarans')->insert(['pendaftaran_id'=>13,'metode_pembayaran_id'=>3,'nominal'=>200000,'status_pembayaran'=>'sukses','order_id'=>null,'snap_token'=>null,'proof_path'=>null,'tanggal_pembayaran'=>$now->copy()->subDays(1)->toDateString(),'catatan'=>'Dibayar tunai di loket TU SMK YPF 2 Cilegon.','verifikasi_oleh'=>1,'verifikasi_tanggal'=>$now->copy()->subDays(1),'created_at'=>$now->copy()->subDays(1),'updated_at'=>$now->copy()->subDays(1)]);
        DB::table('pembayarans')->insert(['pendaftaran_id'=>16,'metode_pembayaran_id'=>2,'nominal'=>200000,'status_pembayaran'=>'menunggu_verifikasi','order_id'=>null,'snap_token'=>null,'proof_path'=>null,'tanggal_pembayaran'=>$now->copy()->subHours(2)->toDateString(),'catatan'=>null,'verifikasi_oleh'=>null,'verifikasi_tanggal'=>null,'created_at'=>$now->copy()->subHours(2),'updated_at'=>$now->copy()->subHours(2)]);
    }
}
