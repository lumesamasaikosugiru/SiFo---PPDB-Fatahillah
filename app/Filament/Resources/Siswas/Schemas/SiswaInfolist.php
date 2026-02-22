<?php

namespace App\Filament\Resources\Siswas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SiswaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('pendaftaran.id')
                    ->label('Pendaftaran')
                    ->placeholder('-'),
                TextEntry::make('nisn'),
                TextEntry::make('nama_siswa'),
                TextEntry::make('jk')
                    ->badge(),
                TextEntry::make('phone'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('agama')
                    ->badge(),
                TextEntry::make('tempat_lahir'),
                TextEntry::make('tanggal_lahir')
                    ->date(),
                TextEntry::make('asal_sekolah'),
                TextEntry::make('tahun_lulus')
                    ->numeric(),
                TextEntry::make('nomor_ijazah'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
