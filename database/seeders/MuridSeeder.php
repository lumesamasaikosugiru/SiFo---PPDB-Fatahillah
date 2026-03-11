<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MuridSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('siswas')->insert([
            [
                'pendaftaran_id' => '1',
                'nisn' => '1234567890',
                'nama_siswa' => 'Araska Ramadhani',
                'jk' => 'laki_laki',
                'phone' => '089099009900',
                'email' => 'araska@gmail.dev',
                'agama' => 'islam',
                'tempat_lahir' => 'Langkas Luwung',
                'tanggal_lahir' => '1999-12-12',
                'alamat' => 'Jl. Langsat Tigapuluh1',
                'asal_sekolah' => 'SMP Langkas 2',
                'tahun_lulus' => '2020',
                'nomor_ijazah' => 'DN-01Mk/09 0000029',
            ],
            [
                'pendaftaran_id' => '2',
                'nisn' => '1234567899',
                'nama_siswa' => 'Riska Putri',
                'jk' => 'perempuan',
                'phone' => '087899009900',
                'email' => 'riska@gmail.dev',
                'agama' => 'islam',
                'tempat_lahir' => 'Makasaras Wangi',
                'tanggal_lahir' => '2000-12-12',
                'alamat' => 'Jl. Makasaras Lembang',
                'asal_sekolah' => 'SMP Pulau 2',
                'tahun_lulus' => '2020',
                'nomor_ijazah' => 'DN-01Mk/09 0000030',
            ],
            [
                'pendaftaran_id' => '3',
                'nisn' => '2234567898',
                'nama_siswa' => 'Yanto Mandani',
                'jk' => 'laki_laki',
                'phone' => '085899009900',
                'email' => 'yanto@gmail.dev',
                'agama' => 'islam',
                'tempat_lahir' => 'Padang Misar',
                'tanggal_lahir' => '1998-12-12',
                'alamat' => 'Jl. Padang Sekali Ambar',
                'asal_sekolah' => 'SMP Padang 2',
                'tahun_lulus' => '2020',
                'nomor_ijazah' => 'DN-01Mk/09 0000031',
            ],
        ]);
    }
}
