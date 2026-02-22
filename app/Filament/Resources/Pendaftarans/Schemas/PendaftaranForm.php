<?php

namespace App\Filament\Resources\Pendaftarans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PendaftaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_regis')
                    ->required(),
                Select::make('tahun_akademik_id')
                    ->relationship('tahunAkademik', 'tahun')
                    ->default(null),
                Select::make('sekolah_id')
                    ->relationship('sekolah', 'nama_sekolah')
                    ->default(null),
                Select::make('jurusan_id')
                    ->relationship('jurusan', 'nama_jurusan')
                    ->default(null),
                Select::make('jalur_pendaftaran')
                    ->options([
                        'reguler' => 'Reguler',
                        'prestasi' => 'Prestasi',
                        'afirmasi' => 'Afirmasi',
                        'pindahan' => 'Pindahan',
                    ])
                    ->required(),
                Select::make('status')
                    ->options([
                        'diproses' => 'Diproses',
                        'diverifikasi' => 'Diverifikasi',
                        'diterima' => 'Diterima',
                        'ditolak' => 'Ditolak',
                        'menunggu_pembayaran' => 'Menunggu pembayaran',
                        'pembayaran_lunas' => 'Pembayaran lunas',
                    ])
                    ->default('diproses')
                    ->required(),
                DatePicker::make('tanggal_submit')
                    ->required(),
                Select::make('dibuat_oleh')
                    ->options(['publik' => 'Publik', 'admin' => 'Admin'])
                    ->default('publik')
                    ->required(),
            ]);
    }
}
