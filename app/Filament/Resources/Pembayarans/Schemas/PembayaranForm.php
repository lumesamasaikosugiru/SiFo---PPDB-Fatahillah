<?php

namespace App\Filament\Resources\Pembayarans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PembayaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('metode_pembayaran_id')
                    ->relationship('metodePembayaran', 'id')
                    ->default(null),
                Select::make('pendaftaran_id')
                    ->relationship('pendaftaran', 'id')
                    ->default(null),
                TextInput::make('nominal')
                    ->required()
                    ->numeric(),
                TextInput::make('order_id')
                    ->default(null),
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
            ]);
    }
}
