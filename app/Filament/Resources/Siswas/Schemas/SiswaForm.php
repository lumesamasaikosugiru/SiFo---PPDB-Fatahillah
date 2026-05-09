<?php

namespace App\Filament\Resources\Siswas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use function Laravel\Prompts\textarea;

class SiswaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Identitas Pendaftaran')
                    ->schema([
                        Select::make('pendaftaran_id')
                            ->label('Nomor Registrasi')
                            ->inlineLabel()
                            ->relationship('pendaftaran', 'kode_regis')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(null),
                        TextInput::make('nomor_ijazah')
                            ->label('Nomor Ijazah')
                            ->inlineLabel()
                            ->required(),
                    ])
                    ->columnSpanFull(),

                Section::make('Data Diri Calon Murid')
                    ->schema([
                        TextInput::make('nisn')
                            ->required(),
                        TextInput::make('nama_siswa')
                            ->required(),
                        Select::make('jk')
                            ->options(['laki_laki' => 'Laki laki', 'perempuan' => 'Perempuan'])
                            ->required(),
                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->required(),
                        TextInput::make('phone')
                            ->tel()
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required(),
                    ])
                    ->iconColor('success')
                    ->icon(Heroicon::OutlinedIdentification)
                    ->columns(2)
                    ->columnSpan(2),

                Section::make('Informasi Tambahan')
                    ->schema([
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
                    ])
                    ->iconColor('success')
                    ->icon(Heroicon::OutlinedInformationCircle)
                    ->columns(2)
                    ->columnSpan(2),

            ])
            ->columns(4);
    }
}
