<?php

namespace App\Filament\Resources\Sekolahs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SekolahForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_sekolah')
                    ->required(),
                Select::make('tingkatan')
                    ->options(['TK' => 'T k', 'TPA' => 'T p a', 'SMP' => 'S m p', 'SMK' => 'S m k', 'S1' => 'S1'])
                    ->required(),
                Textarea::make('alamat')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('kuota')
                    ->required()
                    ->numeric(),
            ]);
    }
}
