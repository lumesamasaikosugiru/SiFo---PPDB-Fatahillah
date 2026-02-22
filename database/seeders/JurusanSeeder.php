<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jurusans')->insert([
            [
                'sekolah_id' => '1',
                'nama_jurusan' => 'Teknik Mesin',
            ],
            [
                'sekolah_id' => '1',
                'nama_jurusan' => 'Teknik Otomotif',
            ],
            [
                'sekolah_id' => '1',
                'nama_jurusan' => 'Teknik Komputer Jaringan',
            ],
            [
                'sekolah_id' => '1',
                'nama_jurusan' => 'Teknik Kimia Industri',
            ],
            [
                'sekolah_id' => '2',
                'nama_jurusan' => 'Teknik Kimia Industri',

            ],
            [
                'sekolah_id' => '2',
                'nama_jurusan' => 'Teknik Mesin',

            ],
            [
                'sekolah_id' => '2',
                'nama_jurusan' => 'Teknik Elektro',

            ],
            [
                'sekolah_id' => '3',
                'nama_jurusan' => 'Teknik Komputer Jaringan',

            ],
            [
                'sekolah_id' => '3',
                'nama_jurusan' => 'Desain Komunikasi Visual',

            ],
        ]);
    }
}
