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
            ],//1
            [
                'sekolah_id' => '1',
                'nama_jurusan' => 'Teknik Otomotif',
            ],//2
            [
                'sekolah_id' => '1',
                'nama_jurusan' => 'Teknik Komputer Jaringan',
            ],//3
            [
                'sekolah_id' => '1',
                'nama_jurusan' => 'Teknik Kimia Industri',
            ],//4
            [
                'sekolah_id' => '2',
                'nama_jurusan' => 'Teknik Kimia Industri',

            ],//5
            [
                'sekolah_id' => '2',
                'nama_jurusan' => 'Teknik Mesin',

            ],//6
            [
                'sekolah_id' => '2',
                'nama_jurusan' => 'Teknik Elektro',

            ],//7
            [
                'sekolah_id' => '3',
                'nama_jurusan' => 'Teknik Komputer Jaringan',

            ],//8
            [
                'sekolah_id' => '3',
                'nama_jurusan' => 'Desain Komunikasi Visual',

            ],//9
        ]);
    }
}
