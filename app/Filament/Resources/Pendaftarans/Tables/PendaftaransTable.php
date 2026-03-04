<?php

namespace App\Filament\Resources\Pendaftarans\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
                // ViewAction::make(),
                // EditAction::make(),
                Action::make('approve')
                    ->label('Ubah Status')
                    ->badge()
                    ->color(Color::Lime)
                    ->icon(Heroicon::QueueList)
                    ->visible(fn() => auth()->user()->can('pendaftaran.update_status'))
                    ->form([
                        Select::make('status')
                            ->label('Status Pendaftaran')
                            ->options([
                                'diproses' => 'Diproses',
                                'diverifikasi' => 'Diverifikasi',
                                'diterima' => 'Diterima',
                                'ditolak' => 'Ditolak',
                                'menunggu_pembayaran' => 'Menunggu Pembayaran',
                                'pembayaran_diproses' => 'Pembayaran Diproses',
                                'pembayaran_lunas' => 'Pembayaran Lunas',
                                'selesai' => 'Selesai',
                            ])
                            ->required(),
                    ])
                    ->action(function (Model $record, array $data) {
                        $record->update([
                            'status' => $data['status'],
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Ubah Status Pendaftaran')
                    ->modalSubmitActionLabel('Simpan')
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
