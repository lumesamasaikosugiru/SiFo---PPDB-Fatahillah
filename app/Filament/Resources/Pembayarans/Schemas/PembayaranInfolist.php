<?php

namespace App\Filament\Resources\Pembayarans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PembayaranInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('metodePembayaran.id')
                    ->label('Metode pembayaran')
                    ->placeholder('-'),
                TextEntry::make('pendaftaran.id')
                    ->label('Pendaftaran')
                    ->placeholder('-'),
                TextEntry::make('nominal')
                    ->numeric(),
                TextEntry::make('order_id')
                    ->placeholder('-'),
                TextEntry::make('status_pembayaran')
                    ->badge(),
                TextEntry::make('tanggal_pembayaran')
                    ->date(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
