<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Preview Foto')
                    ->schema([
                        ImageEntry::make('file_path')
                            ->hiddenLabel()
                            // ->disk('public')
                            ->imageSize(250)
                            ->visible(
                                fn($record) =>
                                str_contains($record->file_path, '.jpg') ||
                                str_contains($record->file_path, '.jpeg') ||
                                str_contains($record->file_path, '.png') ||
                                str_contains($record->file_path, '.webp')
                            )
                            ->extraImgAttributes([
                                'class' => 'rounded-lg object-contain'
                            ]),

                    ])
                    ->columnSpan(1),

                Fieldset::make('Informasi Detail')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')
                            ->label('Alamat Email'),
                        TextEntry::make('AdminSekolah.nama_sekolah')
                            ->label('Sekolah Induk')
                            ->badge()
                            ->placeholder('-'),
                        TextEntry::make('roles.name')
                            ->badge()
                            ->color('warning')
                            ->label('Role'),
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Diubah')
                            ->dateTime()
                            ->placeholder('-'),
                    ])
                    ->columnSpan(2)
            ])
            ->columns(4);
    }
}
