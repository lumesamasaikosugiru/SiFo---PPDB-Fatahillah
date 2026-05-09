<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')


            ->contentGrid([
                'xl' => 4,
                'lg' => 3,
                'md' => 2,
            ])
            ->columns([

                Grid::make([
                    'default' => 1
                ])->schema([
                            Stack::make([
                                ImageColumn::make('file_path')
                                    ->imageSize(200)
                                    ->label('Foto'),
                                TextColumn::make('name')
                                    ->label('Nama')
                                    ->weight('bold')
                                    ->searchable(),
                                TextColumn::make('email')
                                    ->label('Alamat Email')
                                    ->icon(Heroicon::AtSymbol)
                                    ->searchable(),
                                TextColumn::make('roles.name')
                                    ->icon(Heroicon::FingerPrint)
                                    ->label('Role')
                                    ->searchable(),
                                TextColumn::make('is_active')
                                    ->icon(Heroicon::InformationCircle)
                                    ->label('Status')
                                    ->formatStateUsing(fn($state) => $state ? 'Active' : 'Inactive')
                                    ->badge()
                                    ->color(fn($state) => $state ? 'success' : 'danger')
                                    ->sortable()
                            ]),


                        ]),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make()
                ])
                    ->label('Aksi')
                    ->icon(Heroicon::PencilSquare)

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
