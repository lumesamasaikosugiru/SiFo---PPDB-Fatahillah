<?php

namespace App\Filament\Resources\MetodePembayarans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MetodePembayaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('nama_metode')
                    ->options(['otomatis' => 'Otomatis', 'transfer' => 'Transfer', 'cash' => 'Cash'])
                    ->required(),
                TextInput::make('deskripsi')
                    ->default(null),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
