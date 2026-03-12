<?php

namespace App\Filament\Resources\Siswas\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiswasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable(),
                TextColumn::make('nama_siswa')
                    ->label('Nama Calon Murid')
                    ->searchable(),
                TextColumn::make('jk')
                    ->label('Jenis Kelamin')
                    ->badge(),
                TextColumn::make('phone')
                    ->label('Kontak')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Alamat Email')
                    ->searchable(),
                TextColumn::make('asal_sekolah')
                    ->label('Asal Sekolah')
                    ->searchable(),
                TextColumn::make('tahun_lulus')
                    ->label('Tahun Lulus')
                    ->sortable(),
                TextColumn::make('pendaftaran.jalur_pendaftaran')
                    ->label('Jalur Pendaftaran')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),

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
