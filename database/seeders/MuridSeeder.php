<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MuridSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('siswas')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = [
            // pendaftaran_id 1..5 → SMK YPF 1 Kramatwatu
            [1,'1234500001','Budi Santoso','laki_laki','081111111101','budi.s@gmail.dev','islam','Serang','2008-05-10','Jl. Kramatwatu No.1','SMP N 1 Kramatwatu',2024,'DN-KRM-0001'],
            [2,'1234500002','Siti Nurhaliza','perempuan','081111111102','siti.n@gmail.dev','islam','Cilegon','2008-07-20','Jl. Gsi Blok A','SMP N 2 Kramatwatu',2024,'DN-KRM-0002'],
            [3,'1234500003','Reza Firmansyah','laki_laki','081111111103','reza.f@gmail.dev','islam','Serang','2008-09-15','Jl. Gsi Blok B','MTs Fatahillah',2024,'DN-KRM-0003'],
            [4,'1234500004','Putri Handayani','perempuan','081111111104','putri.h@gmail.dev','islam','Cilegon','2009-01-05','Jl. Kramatwatu No.4','SMP N 3 Cilegon',2024,'DN-KRM-0004'],
            [5,'1234500005','Ahmad Fauzi','laki_laki','081111111105','ahmad.f@gmail.dev','islam','Serang','2008-11-30','Jl. Gsi Raya','SMP IT Harapan',2024,'DN-KRM-0005'],
            // 6..10 → SMK YPF 1 Cilegon
            [6,'1234500006','Dewi Rahayu','perempuan','081111111106','dewi.r@gmail.dev','islam','Cilegon','2008-03-22','Jl. Kubang Sepat No.6','SMP N 4 Cilegon',2024,'DN-CIL1-0001'],
            [7,'1234500007','Yusuf Maulana','laki_laki','081111111107','yusuf.m@gmail.dev','islam','Serang','2009-06-14','Jl. Kubang Indah','MTs Al-Fatah',2024,'DN-CIL1-0002'],
            [8,'1234500008','Annisa Fitriani','perempuan','081111111108','annisa.fi@gmail.dev','islam','Cilegon','2008-12-01','Jl. Kubang Sepat No.8','SMP N 1 Cilegon',2024,'DN-CIL1-0003'],
            [9,'1234500009','Fajar Nugroho','laki_laki','081111111109','fajar.n@gmail.dev','islam','Lebak','2009-02-18','Jl. Anyer Dalam','SMP N 5 Cilegon',2024,'DN-CIL1-0004'],
            [10,'1234500010','Maya Sari','perempuan','081111111110','maya.s@gmail.dev','islam','Cilegon','2008-08-25','Jl. Kubang Barat','MTs Hidayatul',2024,'DN-CIL1-0005'],
            // 11..13 → SMK YPF 2 Cilegon
            [11,'1234500011','Rizki Pratama','laki_laki','081111111111','rizki.p@gmail.dev','islam','Cilegon','2008-04-10','Jl. Kubang Sepat No.11','SMP N 2 Cilegon',2024,'DN-CIL2-0001'],
            [12,'1234500012','Fitri Amalia','perempuan','081111111112','fitri.a@gmail.dev','islam','Serang','2009-07-03','Jl. Samber Timur','SMP IT Cilegon',2024,'DN-CIL2-0002'],
            [13,'1234500013','Diki Hermawan','laki_laki','081111111113','diki.h@gmail.dev','islam','Cilegon','2008-10-17','Jl. Kubang Sepat No.13','SMP N 3 Cilegon',2024,'DN-CIL2-0003'],
            // 14..16 → SMP YPF Cilegon
            [14,'1234500014','Lina Marlina','perempuan','081111111114','lina.m@gmail.dev','islam','Cilegon','2011-05-20','Jl. Kubang No.14','SD N 1 Cilegon',2023,'DN-SMP-0001'],
            [15,'1234500015','Eko Prasetyo','laki_laki','081111111115','eko.pr@gmail.dev','islam','Serang','2011-08-12','Jl. Kubang No.15','SD IT Harapan',2023,'DN-SMP-0002'],
            [16,'1234500016','Nadia Putri','perempuan','081111111116','nadia.p@gmail.dev','islam','Cilegon','2011-03-07','Jl. Kubang No.16','SD N 2 Cilegon',2023,'DN-SMP-0003'],
        ];

        foreach ($data as $d) {
            DB::table('siswas')->insert([
                'pendaftaran_id' => $d[0],
                'nisn'           => $d[1],
                'nama_siswa'     => $d[2],
                'jk'             => $d[3],
                'phone'          => $d[4],
                'email'          => $d[5],
                'agama'          => $d[6],
                'tempat_lahir'   => $d[7],
                'tanggal_lahir'  => $d[8],
                'alamat'         => $d[9],
                'asal_sekolah'   => $d[10],
                'tahun_lulus'    => $d[11],
                'nomor_ijazah'   => $d[12],
            ]);
        }
    }
}
