<?php

namespace App\Filament\Resources\Pendaftarans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PendaftaranInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('kode_regis'),
                TextEntry::make('tahunAkademik.id')
                    ->label('Tahun akademik')
                    ->placeholder('-'),
                TextEntry::make('sekolah.id')
                    ->label('Sekolah')
                    ->placeholder('-'),
                TextEntry::make('jurusan.id')
                    ->label('Jurusan')
                    ->placeholder('-'),
                TextEntry::make('jalur_pendaftaran')
                    ->badge(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('tanggal_submit')
                    ->date(),
                TextEntry::make('dibuat_oleh')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
