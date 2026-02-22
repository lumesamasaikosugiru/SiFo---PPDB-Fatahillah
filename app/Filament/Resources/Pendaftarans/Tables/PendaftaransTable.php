<?php

namespace App\Filament\Resources\Pendaftarans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PendaftaransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_regis')
                    ->searchable(),
                TextColumn::make('tahunAkademik.id')
                    ->searchable(),
                TextColumn::make('sekolah.id')
                    ->searchable(),
                TextColumn::make('jurusan.id')
                    ->searchable(),
                TextColumn::make('jalur_pendaftaran')
                    ->badge(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('tanggal_submit')
                    ->date()
                    ->sortable(),
                TextColumn::make('dibuat_oleh')
                    ->badge(),
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
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
