<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('file_path')
                    ->label('Pilih Foto')
                    ->disk('public')
                    ->directory('profil_picture')
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
                    ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                    ->dehydrated(fn(string $state): bool => filled($state))
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
