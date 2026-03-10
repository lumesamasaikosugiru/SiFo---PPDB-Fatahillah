<?php

namespace App\Filament\Resources\Pendaftarans\Pages;

use App\Filament\Resources\Pendaftarans\PendaftaranResource;
use App\Models\Dokumen;
use App\Models\Siswa;
use App\Models\WaliSiswa;
use Filament\Resources\Pages\CreateRecord;

class CreatePendaftaran extends CreateRecord
{
    protected static string $resource = PendaftaranResource::class;

    //kode ini digunakan untuk membuat form pendaftaran dapat mengisi 3 tabel [siswa,wali_siswa,dokumen]
    protected function afterCreate()
    {
        $data = $this->form->getState();

        $pendaftaran = $this->record;

        Siswa::create([
            'pendaftaran_id' => $pendaftaran->id,
            'nama_siswa' => $data['siswa']['nama_siswa'] ?? null,
            'nisn' => $data['siswa']['nisn'] ?? null,
            'jk' => $data['siswa']['jk'] ?? null,
            'agama' => $data['siswa']['agama'] ?? null,
            'tempat_lahir' => $data['siswa']['tempat_lahir'] ?? null,
            'tanggal_lahir' => $data['siswa']['tanggal_lahir'] ?? null,
            'phone' => $data['siswa']['phone'] ?? null,
            'email' => $data['siswa']['email'] ?? null,
            'asal_sekolah' => $data['siswa']['asal_sekolah'] ?? null,
            'tahun_lulus' => $data['siswa']['tahun_lulus'] ?? null,
            'nomor_ijazah' => $data['siswa']['nomor_ijazah'] ?? null,
        ]);


        WaliSiswa::create([
            'pendaftaran_id' => $pendaftaran->id,
            'nama_wali' => $data['waliSiswa']['nama_wali'] ?? null,
            'hubungan' => $data['waliSiswa']['hubungan'] ?? null,
            'pekerjaan' => $data['waliSiswa']['pekerjaan'] ?? null,
            'notelp_wali' => $data['waliSiswa']['notelp_wali'] ?? null,
            'email' => $data['waliSiswa']['email'] ?? null,
        ]);

        if (isset($data['dokumens'])) {
            foreach ($data['dokumens'] as $tipe => $file) {
                Dokumen::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'tipe_dokumen' => $tipe,
                    'file_path' => $file,

                ]);
            }
        }
    }
}
