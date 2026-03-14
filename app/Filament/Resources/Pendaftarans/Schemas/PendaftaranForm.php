<?php

namespace App\Filament\Resources\Pendaftarans\Schemas;

use App\Models\Jurusan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
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

                Wizard::make([
                    Step::make('Pendaftaran')
                        ->schema([
                            Section::make('Informasi Sekolah')
                                ->schema([
                                    Select::make('sekolah_id')
                                        ->relationship('sekolah', 'nama_sekolah')
                                        ->afterStateUpdated(function (Set $set) {
                                            $set('jurusan_id', '');
                                        })
                                        ->disabled(fn() => auth()->user()->adminSekolah !== null),

                                    Hidden::make('sekolah_id')
                                        ->default(fn() => auth()->user()->adminSekolah?->sekolah_id)
                                        ->visible(fn() => auth()->user()->adminSekolah !== null),

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
                                ])
                                ->iconColor('success')
                                ->icon(Heroicon::OutlinedBuildingLibrary)
                                ->columns(1),

                            Section::make('Status Sistem')
                                ->schema([
                                    Select::make('status')
                                        ->options([
                                            'diproses' => 'Diproses',
                                            'diverifikasi' => 'Diverifikasi',
                                            'diterima' => 'Diterima',
                                            'ditolak' => 'Ditolak',
                                            'menunggu_pembayaran' => 'Menunggu pembayaran',
                                            // 'pembayaran_lunas' => 'Pembayaran lunas',
                                        ])
                                        ->default('diproses')
                                        ->required(),

                                    Select::make('dibuat_oleh')
                                        ->options(['publik' => 'Publik', 'admin' => 'Admin'])
                                        ->default('publik')
                                        ->required(),
                                ])
                                ->iconColor('success')
                                ->icon(Heroicon::OutlinedMegaphone)
                                ->columns(1),

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
                                ->iconColor('success')
                                ->icon(Heroicon::OutlinedClipboardDocumentList)
                                ->columns(3)
                                ->columnSpanFull(),

                        ]),//end-step1-schema

                    Step::make('Data Diri Calon Murid')
                        ->schema([
                            Section::make('Identitas Murid')
                                ->schema([
                                    TextInput::make('siswa.nama_siswa')
                                        ->required(),
                                    TextInput::make('siswa.phone')
                                        ->required(),
                                    TextInput::make('siswa.email')
                                        ->required(),
                                    Select::make('siswa.jk')
                                        ->options([
                                            'laki_laki' => 'Laki-Laki',
                                            'perempuan' => 'Perempuan',
                                        ])
                                        ->placeholder('Pilih Jenis Kelamin')
                                        ->required(),

                                ])
                                ->iconColor('success')
                                ->icon(Heroicon::OutlinedIdentification),

                            Section::make('Asal Sekolah')
                                ->schema([
                                    TextInput::make('siswa.nisn')
                                        ->required(),
                                    TextInput::make('siswa.asal_sekolah')
                                        ->required(),
                                    TextInput::make('siswa.tahun_lulus')
                                        ->required(),
                                    TextInput::make('siswa.nomor_ijazah')
                                        ->required(),

                                ])
                                ->iconColor('success')
                                ->icon(Heroicon::OutlinedAcademicCap),
                            Section::make('Informasi Tambahan')
                                ->schema([
                                    Textarea::make('siswa.alamat')
                                        ->label('Alamat')
                                        ->required(),
                                    Select::make('siswa.agama')
                                        ->options([
                                            'islam' => 'Islam',
                                            'protestan' => 'Protestan',
                                            'katolik' => 'Katolik',
                                            'hindu' => 'Hindu',
                                            'budha' => 'Budha',
                                            'khonghucu' => 'Khonghucu',
                                        ])
                                        ->placeholder('Pilih Agama')
                                        ->required(),

                                    TextInput::make('siswa.tempat_lahir')
                                        ->required(),
                                    DatePicker::make('siswa.tanggal_lahir')
                                        ->required(),
                                ])
                                ->iconColor('success')
                                ->icon(Heroicon::OutlinedInformationCircle)
                                ->columnSpanFull(),




                        ]),//end-step2-schema

                    Step::make('Data Calon Wali Murid')
                        ->schema([
                            Section::make('Informasi Wali Murid')
                                ->schema([
                                    TextInput::make('waliSiswa.nama_wali')
                                        ->required(),
                                    Select::make('waliSiswa.hubungan')
                                        ->options([
                                            'bapak' => 'Bapak',
                                            'ibu' => 'Ibu',
                                            'saudara_kandung' => 'Saudara Kandung',
                                            'saudara_keluarga' => 'Saudara Keluarga',
                                        ])
                                        ->placeholder('Pilih Jenis Hubungan')
                                        ->required(),
                                    Textarea::make('waliSiswa.alamat')
                                        ->label('Alamat')
                                        ->required(),
                                    TextInput::make('waliSiswa.pekerjaan')
                                        ->required(),
                                    TextInput::make('waliSiswa.notelp_wali')
                                        ->required(),
                                    TextInput::make('waliSiswa.email')
                                        ->required(),
                                ])
                                ->iconColor('success')
                                ->icon(Heroicon::OutlinedUser)
                                ->columns(2)
                                ->columnSpanFull()
                        ]),//end-step3-schema

                    Step::make('Dokumen Persyaratan')
                        ->schema([
                            Section::make('Upload Dokumen')
                                ->schema([
                                    FileUpload::make('dokumens.foto')
                                        ->label('Pas Foto')
                                        ->disk('public')
                                        ->directory('dokumen')
                                        ->required(),

                                    FileUpload::make('dokumens.ijazah')
                                        ->label('Ijazah / SKL')
                                        ->disk('public')
                                        ->directory('dokumen')
                                        ->required(),

                                    FileUpload::make('dokumens.kk')
                                        ->label('Kartu Keluarga')
                                        ->disk('public')
                                        ->directory('dokumen')
                                        ->required(),

                                    FileUpload::make('dokumens.akta')
                                        ->label('Akta Kelahiran')
                                        ->disk('public')
                                        ->directory('dokumen')
                                        ->required(),
                                ])
                                ->iconColor('success')
                                ->icon(Heroicon::OutlinedDocumentArrowUp)
                                ->columns(2)
                                ->columnSpanFull()
                        ])
                        ->icon(Heroicon::ClipboardDocument)

                ])//end-wizard
                    ->columnSpanFull()
                    ->columns(2)
                    ->skippable(),
            ]);
    }
}
