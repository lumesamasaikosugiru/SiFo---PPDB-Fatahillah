<?php

namespace App\Filament\Resources\Sekolahs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SekolahInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nama_sekolah'),
                TextEntry::make('tingkatan')
                    ->badge(),
                TextEntry::make('alamat')
                    ->columnSpanFull(),
                TextEntry::make('kuota')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
