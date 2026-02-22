<?php

namespace App\Filament\Resources\Siswas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SiswaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('pendaftaran_id')
                    ->relationship('pendaftaran', 'id')
                    ->default(null),
                TextInput::make('nisn')
                    ->required(),
                TextInput::make('nama_siswa')
                    ->required(),
                Select::make('jk')
                    ->options(['laki_laki' => 'Laki laki', 'perempuan' => 'Perempuan'])
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                Select::make('agama')
                    ->options([
            'islam' => 'Islam',
            'protestan' => 'Protestan',
            'katolik' => 'Katolik',
            'hindu' => 'Hindu',
            'budha' => 'Budha',
            'khonghucu' => 'Khonghucu',
        ])
                    ->required(),
                TextInput::make('tempat_lahir')
                    ->required(),
                DatePicker::make('tanggal_lahir')
                    ->required(),
                TextInput::make('asal_sekolah')
                    ->required(),
                TextInput::make('tahun_lulus')
                    ->required()
                    ->numeric(),
                TextInput::make('nomor_ijazah')
                    ->required(),
            ]);
    }
}
