<?php

namespace App\Filament\Resources\Jurusans\Schemas;

use App\Models\Sekolah;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class JurusanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('sekolah_id')
                    ->label('Sekolah')
                    ->options(function () {
                        $user = auth()->user();
                        // Admin sekolah: hanya bisa pilih sekolahnya sendiri
                        if (!$user->hasAnyRole(['superadmin', 'admin_yayasan'])) {
                            $sid = $user->adminSekolah?->sekolah_id;
                            if ($sid) {
                                $nama = Sekolah::find($sid)?->nama_sekolah ?? '-';
                                return [$sid => $nama];
                            }
                            return [];
                        }
                        // Superadmin / admin yayasan: semua sekolah
                        return Sekolah::orderBy('nama_sekolah')
                            ->pluck('nama_sekolah', 'id')
                            ->toArray();
                    })
                    ->default(function () {
                        $user = auth()->user();
                        // Auto-select sekolah untuk admin sekolah
                        if (!$user->hasAnyRole(['superadmin', 'admin_yayasan'])) {
                            return $user->adminSekolah?->sekolah_id;
                        }
                        return null;
                    })
                    ->searchable()
                    ->required(),

                TextInput::make('nama_jurusan')
                    ->label('Nama Jurusan')
                    ->required(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->required(),
            ]);
    }
}
