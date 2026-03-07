<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('file_path')
                    ->label('Pilih Foto')
                    ->default(null),
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                TextInput::make('email')
                    ->label('Alamat Email')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Select::make('roles')
                    ->label('Role')
                    // ->relationship('roles', 'name')
                    ->options([
                        'superadmin' => 'Admin Sakti',
                        'admin_yayasan' => 'Admin Yayasan',
                        'admin_sekolah' => 'Admin Sekolah',
                        'kepala_sekolah_smp' => 'Kepala Sekolah (SMP)',
                        'kepala_sekolah_smk' => 'Kepala Sekolah (SMK)',
                    ])
                    ->preload(),
            ]);
    }
}
