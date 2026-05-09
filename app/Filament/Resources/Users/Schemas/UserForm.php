<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Fieldset::make('Set Profile Picture')
                    ->schema([
                        FileUpload::make('file_path')
                            ->hiddenLabel()
                            // ->disk('public')
                            ->directory('profil_picture')
                            ->default(null),

                    ])
                    ->columns(1)
                    ->columnSpan(1),

                Section::make('Detail Informasi')
                    ->schema([
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
                            ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn($operation) => $operation === 'create'),
                        Select::make('roles')
                            ->label('Role')
                            ->relationship('roles', 'name')
                            // ->options([
                            //     'superadmin' => 'Admin Sakti',
                            //     'admin_yayasan' => 'Admin Yayasan',
                            //     'admin_sekolah' => 'Admin Sekolah',
                            //     'kepala_sekolah_smp' => 'Kepala Sekolah (SMP)',
                            //     'kepala_sekolah_smk' => 'Kepala Sekolah (SMK)',
                            // ])
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('is_active')
                            ->label('Status')
                            ->options([
                                1 => 'Active',
                                0 => 'Inactive',
                            ])
                            ->required(),

                    ])
                    ->iconColor('success')
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->columns(2)
                    ->columnSpan(2),
            ])->columns(3);
    }
}
