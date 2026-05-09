<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WaliMuridSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('wali_siswas')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = [
            [1,'Bambang Santoso','bapak','Jl. Kramatwatu No.1','Pegawai Swasta','081200000101','bambang.s@gmail.dev'],
            [2,'Siti Aminah','ibu','Jl. Gsi Blok A','Ibu Rumah Tangga','081200000102','siti.a@gmail.dev'],
            [3,'Hendra Firmansyah','bapak','Jl. Gsi Blok B','Wiraswasta','081200000103','hendra.f@gmail.dev'],
            [4,'Ratna Handayani','ibu','Jl. Kramatwatu No.4','Guru','081200000104','ratna.h@gmail.dev'],
            [5,'Joko Fauzi','bapak','Jl. Gsi Raya No.5','TNI','081200000105','joko.f@gmail.dev'],
            [6,'Wahyu Rahayu','bapak','Jl. Kubang Sepat No.6','Pedagang','081200000106','wahyu.r@gmail.dev'],
            [7,'Halimah Maulana','ibu','Jl. Kubang Indah No.7','Ibu Rumah Tangga','081200000107','halimah.m@gmail.dev'],
            [8,'Darmawan Fitriani','bapak','Jl. Kubang Sepat No.8','POLRI','081200000108','darmawan.f@gmail.dev'],
            [9,'Suryati Nugroho','ibu','Jl. Anyer Dalam No.9','Petani','081200000109','suryati.n@gmail.dev'],
            [10,'Agus Sari','bapak','Jl. Kubang Barat No.10','Pegawai Negeri','081200000110','agus.s@gmail.dev'],
            [11,'Dewi Pratama','ibu','Jl. Kubang Sepat No.11','Bidan','081200000111','dewi.p@gmail.dev'],
            [12,'Rian Amalia','bapak','Jl. Samber Timur No.12','Wiraswasta','081200000112','rian.a@gmail.dev'],
            [13,'Yayah Hermawan','ibu','Jl. Kubang Sepat No.13','Ibu Rumah Tangga','081200000113','yayah.h@gmail.dev'],
            [14,'Encep Marlina','bapak','Jl. Kubang No.14','Buruh','081200000114','encep.m@gmail.dev'],
            [15,'Titi Prasetyo','ibu','Jl. Kubang No.15','Ibu Rumah Tangga','081200000115','titi.p@gmail.dev'],
            [16,'Dadan Putri','bapak','Jl. Kubang No.16','Pedagang','081200000116','dadan.p@gmail.dev'],
        ];

        foreach ($data as $d) {
            DB::table('wali_siswas')->insert([
                'pendaftaran_id' => $d[0],
                'nama_wali'      => $d[1],
                'hubungan'       => $d[2],
                'alamat'         => $d[3],
                'pekerjaan'      => $d[4],
                'notelp_wali'    => $d[5],
                'email'          => $d[6],
            ]);
        }
    }
}
