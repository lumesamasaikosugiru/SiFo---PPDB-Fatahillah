<?php

namespace App\Filament\Resources\Pembayarans\Schemas;

use App\Models\Pendaftaran;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PembayaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('order_id')
                    ->default(Str::random(20))
                    ->unique(ignoreRecord: true),
                Section::make('Detail Pendaftaran')
                    ->schema([
                        Select::make('pendaftaran_id')
                            ->relationship('pendaftaran', 'kode_regis', function ($query) {
                                $query->with(['siswa', 'sekolah', 'jurusan'])
                                    ->where('status', ['diterima', 'menunggu_pembayaran'])
                                    ->whereDoesntHave('pembayarans');

                                $user = auth()->user();

                                if ($user->adminSekolah?->sekolah_id) {
                                    $query->where('sekolah_id', $user->adminSekolah->sekolah_id);
                                }
                            })

                            ->getOptionLabelFromRecordUsing(fn($record) => ($record->siswa?->nama_siswa ?? 'Nama belum diisi') . ' | ' . $record->kode_regis . ' | ' . ($record->sekolah?->nama_sekolah ?? '-') . ' | ' . ($record->jurusan?->nama_jurusan ?? '-'))

                            ->getSearchResultsUsing(function (string $search) {

                                $user = auth()->user();

                                $query = Pendaftaran::query()
                                    ->with(['siswa', 'sekolah', 'jurusan'])
                                    ->where(function ($q) use ($search) {
                                        $q->where('kode_regis', 'like', "%{$search}%")
                                            ->orWhereHas('siswa', function ($q2) use ($search) {
                                                $q2->where('nama_siswa', 'like', "%{$search}%");
                                            });
                                    });

                                if ($user->adminSekolah?->sekolah_id) {
                                    $query->where('sekolah_id', $user->adminSekolah->sekolah_id);
                                }

                                return $query->limit(50)->get()->mapWithKeys(function ($record) {
                                    return [
                                        $record->id =>
                                            ($record->siswa?->nama_siswa ?? 'Nama belum diisi') . ' | ' . $record->kode_regis . ' | ' . ($record->sekolah?->nama_sekolah ?? '-') . ' | ' . ($record->jurusan?->nama_jurusan ?? '-')
                                    ];
                                });


                            })
                            ->disableLabel()
                            ->placeholder('Pilih Pendaftar')
                            ->searchable()
                            ->preload()
                            ->default(null),

                    ])
                    ->columnSpanFull(),

                Section::make('Detail Pembayaran')
                    ->schema([
                        Select::make('metode_pembayaran_id')
                            ->relationship('metodePembayaran', 'deskripsi', modifyQueryUsing: fn($query) => $query->where('is_active', true))
                            ->placeholder('Pilih metode pembayaran')
                            ->searchable()
                            ->preload()
                            ->default(null),

                        TextInput::make('nominal')
                            ->required()
                            ->prefix('IDR')
                            ->numeric(),
                    ]),

                Section::make('Status Pendaftaran')
                    ->schema([

                        Select::make('status_pembayaran')
                            ->options([
                                'pending' => 'Pending',
                                'menunggu_verifikasi' => 'Menunggu verifikasi',
                                'sukses' => 'Sukses',
                                'gagal' => 'Gagal',
                                'kadaluarsa' => 'Kadaluarsa',
                            ])
                            ->default('pending')
                            ->required(),
                        DatePicker::make('tanggal_pembayaran')
                            ->required(),
                    ]),



            ]);
    }
}
