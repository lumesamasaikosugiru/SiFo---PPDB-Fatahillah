<?php

namespace App\Filament\Resources\Siswas\RelationManagers;

use Filament\Actions\ActionGroup;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WaliSiswasRelationManager extends RelationManager
{
    protected static string $relationship = 'waliSiswas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('pendaftaran_id')
                    ->relationship('pendaftaran', 'kode_regis')
                    ->columnSpanFull()
                    ->required(),

                Section::make('Status Hubungan')
                    ->schema([
                        TextInput::make('nama_wali')
                            ->label('Nama Wali')
                            ->required(),
                        Select::make('hubungan')
                            ->label('Hubungan Keluarga')
                            ->options([
                                'bapak' => 'Bapak',
                                'ibu' => 'Ibu',
                                'saudara_kandung' => 'Saudara kandung',
                                'saudara_keluarga' => 'Saudara keluarga',
                            ])
                            ->required(),
                        TextInput::make('pekerjaan')
                            ->required(),
                    ])
                    ->iconColor('success')
                    ->icon(Heroicon::OutlinedUserGroup),

                Section::make('Informasi Lainnya')
                    ->schema([

                        Textarea::make('alamat')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('notelp_wali')
                            ->label('Kontak Personal')
                            ->tel()
                            ->default(null),
                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->default(null),
                    ])
                    ->iconColor('success')
                    ->icon(Heroicon::OutlinedInformationCircle),


            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([

                Fieldset::make('Info Detail Wali Murid')
                    ->schema([
                        TextEntry::make('pendaftaran.id')
                            ->label('ID Pendaftaran'),
                        TextEntry::make('nama_wali')
                            ->label('Nama Wali'),
                        TextEntry::make('alamat')
                            ->columnSpanFull(),
                        TextEntry::make('hubungan')
                            ->badge(),
                        TextEntry::make('pekerjaan'),
                        TextEntry::make('notelp_wali')
                            ->label('Kontak Personal')
                            ->placeholder('-'),
                        TextEntry::make('email')
                            ->label('Alamat Email')
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                    ])
                    ->columns(3)
                    ->columnSpan(4)
            ])
            ->columns(4);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_wali')
            ->columns([
                TextColumn::make('pendaftaran.kode_regis')
                    ->label('Kode Registrasi'),
                TextColumn::make('nama_wali')
                    ->label('Nama Wali')
                    ->searchable(),
                TextColumn::make('hubungan')
                    ->badge(),
                TextColumn::make('pekerjaan')
                    ->searchable(),
                TextColumn::make('notelp_wali')
                    ->label('Kontak Personal')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Alamat Email')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DissociateAction::make(),
                    DeleteAction::make(),
                ])
                    ->label('Aksi')
                    ->icon(Heroicon::PencilSquare),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
