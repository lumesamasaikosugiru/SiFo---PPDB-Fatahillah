<?php

namespace App\Filament\Resources\Pendaftarans\Schemas;

use App\Models\Jurusan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PendaftaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // TextInput::make('kode_regis')
                Hidden::make('kode_regis')
                    ->default('PPDB26-' . Str::upper(Str::random(8)))
                    ->unique(ignoreRecord: true)
                    ->disabled()
                    ->dehydrated()
                    ->required(),

                Section::make('Informasi Sekolah')
                    ->schema([
                        Select::make('sekolah_id')
                            ->relationship('sekolah', 'nama_sekolah')
                            ->afterStateUpdated(function (Set $set) {
                                $set('jurusan_id', '');
                            })
                            ->dehydrated()
                            ->disabled(),
                        Hidden::make('sekolah_id')
                            ->default(fn() => auth()->user()->adminSekolah->sekolah_id),

                        Select::make('jurusan_id')
                            ->relationship('jurusan', 'nama_jurusan')
                            ->options(function (Get $get) {
                                $sekolahId = $get('sekolah_id');

                                if (!$sekolahId) {
                                    return [];
                                }
                                return Jurusan::query()
                                    ->where('sekolah_id', $sekolahId)
                                    ->pluck('nama_jurusan', 'id');
                            })
                            ->placeholder('Pilih Jurusan')
                            ->preload()
                            ->searchable()
                            ->required(),
                    ]),

                Section::make('Status Sistem')
                    ->schema([
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

                        Select::make('dibuat_oleh')
                            ->options(['publik' => 'Publik', 'admin' => 'Admin'])
                            ->default('publik')
                            ->required(),
                    ]),

                Section::make('Informasi Pendaftaran')
                    ->schema([
                        Select::make('tahun_akademik_id')
                            ->relationship('tahunAkademik', 'tahun')
                            ->placeholder('Pilih Tahun Akademik')
                            ->preload()
                            ->searchable()
                            ->default(null),

                        Select::make('jalur_pendaftaran')
                            ->options([
                                'reguler' => 'Reguler',
                                'prestasi' => 'Prestasi',
                                'afirmasi' => 'Afirmasi',
                                'pindahan' => 'Pindahan',
                            ])
                            ->preload()
                            ->searchable()
                            ->placeholder('Pilih Jalur Pendaftaran')
                            ->required(),
                        DatePicker::make('tanggal_submit')
                            ->required()
                            ->default(now()),
                    ])
                    ->columnSpanFull()
                    ->columns(3),
            ]);
    }
}
