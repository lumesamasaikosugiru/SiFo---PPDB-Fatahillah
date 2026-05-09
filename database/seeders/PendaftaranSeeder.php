<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PendaftaranSeeder extends Seeder
{
    public function run(): void
    {
        // Disable FK checks dulu agar truncate tidak error
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('pembayarans')->truncate();
        DB::table('dokumens')->truncate();
        DB::table('wali_siswas')->truncate();
        DB::table('siswas')->truncate();
        DB::table('pendaftarans')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $now = Carbon::now();

        DB::table('pendaftarans')->insert([
            // SMK YPF 1 Kramatwatu (sekolah_id=1)
            ['kode_regis'=>'PPDB26-AAA11111','tahun_akademik_id'=>4,'sekolah_id'=>1,'jurusan_id'=>1,'jalur_pendaftaran'=>'reguler','status'=>'diproses','tanggal_submit'=>'2026-03-01','dibuat_oleh'=>'publik','created_at'=>$now->copy()->subDays(14),'updated_at'=>$now->copy()->subDays(14)],
            ['kode_regis'=>'PPDB26-AAA22222','tahun_akademik_id'=>4,'sekolah_id'=>1,'jurusan_id'=>2,'jalur_pendaftaran'=>'prestasi','status'=>'diverifikasi','tanggal_submit'=>'2026-03-02','dibuat_oleh'=>'publik','created_at'=>$now->copy()->subDays(13),'updated_at'=>$now->copy()->subDays(12)],
            ['kode_regis'=>'PPDB26-AAA33333','tahun_akademik_id'=>4,'sekolah_id'=>1,'jurusan_id'=>3,'jalur_pendaftaran'=>'reguler','status'=>'diterima','tanggal_submit'=>'2026-03-03','dibuat_oleh'=>'publik','created_at'=>$now->copy()->subDays(12),'updated_at'=>$now->copy()->subDays(10)],
            ['kode_regis'=>'PPDB26-AAA44444','tahun_akademik_id'=>4,'sekolah_id'=>1,'jurusan_id'=>4,'jalur_pendaftaran'=>'afirmasi','status'=>'ditolak','tanggal_submit'=>'2026-03-04','dibuat_oleh'=>'publik','created_at'=>$now->copy()->subDays(11),'updated_at'=>$now->copy()->subDays(9)],
            ['kode_regis'=>'PPDB26-AAA55555','tahun_akademik_id'=>4,'sekolah_id'=>1,'jurusan_id'=>1,'jalur_pendaftaran'=>'reguler','status'=>'menunggu_pembayaran','tanggal_submit'=>'2026-03-05','dibuat_oleh'=>'publik','created_at'=>$now->copy()->subDays(10),'updated_at'=>$now->copy()->subDays(8)],
            // SMK YPF 1 Cilegon (sekolah_id=2)
            ['kode_regis'=>'PPDB26-BBB11111','tahun_akademik_id'=>4,'sekolah_id'=>2,'jurusan_id'=>5,'jalur_pendaftaran'=>'reguler','status'=>'pembayaran_diproses','tanggal_submit'=>'2026-03-06','dibuat_oleh'=>'publik','created_at'=>$now->copy()->subDays(9),'updated_at'=>$now->copy()->subDays(7)],
            ['kode_regis'=>'PPDB26-BBB22222','tahun_akademik_id'=>4,'sekolah_id'=>2,'jurusan_id'=>6,'jalur_pendaftaran'=>'pindahan','status'=>'pembayaran_lunas','tanggal_submit'=>'2026-03-07','dibuat_oleh'=>'publik','created_at'=>$now->copy()->subDays(8),'updated_at'=>$now->copy()->subDays(5)],
            ['kode_regis'=>'PPDB26-BBB33333','tahun_akademik_id'=>4,'sekolah_id'=>2,'jurusan_id'=>7,'jalur_pendaftaran'=>'reguler','status'=>'selesai','tanggal_submit'=>'2026-03-08','dibuat_oleh'=>'publik','created_at'=>$now->copy()->subDays(7),'updated_at'=>$now->copy()->subDays(3)],
            ['kode_regis'=>'PPDB26-BBB44444','tahun_akademik_id'=>4,'sekolah_id'=>2,'jurusan_id'=>5,'jalur_pendaftaran'=>'prestasi','status'=>'diproses','tanggal_submit'=>'2026-03-09','dibuat_oleh'=>'publik','created_at'=>$now->copy()->subDays(6),'updated_at'=>$now->copy()->subDays(6)],
            ['kode_regis'=>'PPDB26-BBB55555','tahun_akademik_id'=>4,'sekolah_id'=>2,'jurusan_id'=>6,'jalur_pendaftaran'=>'reguler','status'=>'diterima','tanggal_submit'=>'2026-03-10','dibuat_oleh'=>'publik','created_at'=>$now->copy()->subDays(5),'updated_at'=>$now->copy()->subDays(4)],
            // SMK YPF 2 Cilegon (sekolah_id=3)
            ['kode_regis'=>'PPDB26-CCC11111','tahun_akademik_id'=>4,'sekolah_id'=>3,'jurusan_id'=>8,'jalur_pendaftaran'=>'reguler','status'=>'diterima','tanggal_submit'=>'2026-03-11','dibuat_oleh'=>'publik','created_at'=>$now->copy()->subDays(4),'updated_at'=>$now->copy()->subDays(3)],
            ['kode_regis'=>'PPDB26-CCC22222','tahun_akademik_id'=>4,'sekolah_id'=>3,'jurusan_id'=>9,'jalur_pendaftaran'=>'prestasi','status'=>'menunggu_pembayaran','tanggal_submit'=>'2026-03-12','dibuat_oleh'=>'publik','created_at'=>$now->copy()->subDays(3),'updated_at'=>$now->copy()->subDays(2)],
            ['kode_regis'=>'PPDB26-CCC33333','tahun_akademik_id'=>4,'sekolah_id'=>3,'jurusan_id'=>8,'jalur_pendaftaran'=>'reguler','status'=>'pembayaran_lunas','tanggal_submit'=>'2026-03-13','dibuat_oleh'=>'publik','created_at'=>$now->copy()->subDays(2),'updated_at'=>$now->copy()->subDays(1)],
            // SMP YPF Cilegon (sekolah_id=4)
            ['kode_regis'=>'PPDB26-DDD11111','tahun_akademik_id'=>4,'sekolah_id'=>4,'jurusan_id'=>null,'jalur_pendaftaran'=>'reguler','status'=>'diproses','tanggal_submit'=>'2026-03-14','dibuat_oleh'=>'publik','created_at'=>$now->copy()->subDays(1),'updated_at'=>$now->copy()->subDays(1)],
            ['kode_regis'=>'PPDB26-DDD22222','tahun_akademik_id'=>4,'sekolah_id'=>4,'jurusan_id'=>null,'jalur_pendaftaran'=>'afirmasi','status'=>'diterima','tanggal_submit'=>'2026-03-15','dibuat_oleh'=>'publik','created_at'=>$now->copy()->subHours(12),'updated_at'=>$now->copy()->subHours(6)],
            ['kode_regis'=>'PPDB26-DDD33333','tahun_akademik_id'=>4,'sekolah_id'=>4,'jurusan_id'=>null,'jalur_pendaftaran'=>'reguler','status'=>'pembayaran_diproses','tanggal_submit'=>'2026-03-16','dibuat_oleh'=>'publik','created_at'=>$now->copy()->subHours(3),'updated_at'=>$now->copy()->subHours(1)],
        ]);
    }
}
