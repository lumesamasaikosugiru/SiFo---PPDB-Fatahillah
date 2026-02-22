<?php

namespace App\Filament\Resources\Jurusans\Schemas;

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
                    ->relationship('sekolah', 'id')
                    ->required(),
                TextInput::make('nama_jurusan')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
