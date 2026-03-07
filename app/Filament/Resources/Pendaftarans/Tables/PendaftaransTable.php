<?php

namespace App\Filament\Resources\Pendaftarans\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
// use Filament\Actions\EditAction;
// use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PendaftaransTable
{

    protected static function updateStatus(Model $record, string $status): void
    {
        $record->update([
            'status' => $status,
            // 'status_updated_by' => auth()->id(),
            // 'status_updated_at' => now(),
        ]);

        Notification::make()
            ->title('Status pendaftaran diperbarui')
            ->body("Status berhasil diubah menjadi: {$status}")
            ->success()
            ->send();
    }
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_regis')
                    ->label('Kode Registrasi')
                    ->searchable(),
                TextColumn::make('TahunAkademik.tahun')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('sekolah.nama_sekolah')
                    ->searchable(),
                TextColumn::make('jurusan.nama_jurusan')
                    ->searchable(),
                TextColumn::make('jalur_pendaftaran')
                    ->label('Jalur Pendaftaran')
                    ->badge(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'info' => 'diverifikasi',
                        'success' => 'diterima',
                        'danger' => 'ditolak',
                        'secondary' => 'pembayaran_lunas',
                        'warning' => 'menunggu_pembayaran',
                        'light' => 'selesai',
                    ]),
                TextColumn::make('tanggal_submit')
                    ->label('Tanggal Submit')
                    ->date()
                    ->sortable(),
                TextColumn::make('diverifikasi_oleh')
                    ->label('Diverifikasi Oleh'),
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
                ActionGroup::make([
                    Action::make('diproses')
                        ->label('Diproses')
                        ->color(Color::Green)
                        ->icon(Heroicon::DocumentArrowUp)
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Perubahan Status')
                        ->modalDescription('Apakah anda yakin ingin mengubah Status Pendaftaran')
                        ->modalSubmitActionLabel('Ya, Ubah Status')
                        ->action(fn(Model $record) => self::updateStatus($record, 'diproses')),

                    Action::make('diverifikasi')
                        ->label('Diverifikasi')
                        ->color(Color::Green)
                        ->icon(Heroicon::DocumentText)
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Perubahan Status')
                        ->modalDescription('Apakah anda yakin ingin mengubah Status Pendaftaran')
                        ->modalSubmitActionLabel('Ya, Ubah Status')
                        ->action(fn(Model $record) => self::updateStatus($record, 'diverifikasi')),

                    Action::make('diterima')
                        ->label('Diterima')
                        ->color(Color::Green)
                        ->icon(Heroicon::DocumentCheck)
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Perubahan Status')
                        ->modalDescription('Apakah anda yakin ingin mengubah Status Pendaftaran')
                        ->modalSubmitActionLabel('Ya, Ubah Status')
                        ->action(fn(Model $record) => self::updateStatus($record, 'diterima')),

                    Action::make('ditolak')
                        ->label('Ditolak')
                        ->color(Color::Green)
                        ->icon(Heroicon::ExclamationTriangle)
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Perubahan Status')
                        ->modalDescription('Apakah anda yakin ingin mengubah Status Pendaftaran')
                        ->modalSubmitActionLabel('Ya, Ubah Status')
                        ->action(fn(Model $record) => self::updateStatus($record, 'ditolak')),

                    Action::make('menunggu_pembayaran')
                        ->label('Menunggu Pembayaran')
                        ->color(Color::Green)
                        ->icon(Heroicon::DocumentCurrencyDollar)
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Perubahan Status')
                        ->modalDescription('Apakah anda yakin ingin mengubah Status Pendaftaran')
                        ->modalSubmitActionLabel('Ya, Ubah Status')
                        ->action(fn(Model $record) => self::updateStatus($record, 'menunggu_pembayaran')),

                    Action::make('pembayaran_diproses')
                        ->label('Pembayaran Diproses')
                        ->color(Color::Green)
                        ->icon(Heroicon::Banknotes)
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Perubahan Status')
                        ->modalDescription('Apakah anda yakin ingin mengubah Status Pendaftaran')
                        ->modalSubmitActionLabel('Ya, Ubah Status')
                        ->action(fn(Model $record) => self::updateStatus($record, 'pembayaran_diproses')),

                    Action::make('pembayaran_lunas')
                        ->label('Pembayaran Lunas')
                        ->color(Color::Green)
                        ->icon(Heroicon::CurrencyDollar)
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Perubahan Status')
                        ->modalDescription('Apakah anda yakin ingin mengubah Status Pendaftaran')
                        ->modalSubmitActionLabel('Ya, Ubah Status')
                        ->action(fn(Model $record) => self::updateStatus($record, 'pembayaran_lunas')),

                    Action::make('selesai')
                        ->label('Selesai')
                        ->color(Color::Green)
                        ->icon(Heroicon::CheckBadge)
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Perubahan Status')
                        ->modalDescription('Apakah anda yakin ingin mengubah Status Pendaftaran')
                        ->modalSubmitActionLabel('Ya, Ubah Status')
                        ->action(fn(Model $record) => self::updateStatus($record, 'selesai')),



                ])
                    ->label('Ubah Status')
                    ->icon(Heroicon::PencilSquare)
                    ->visible(fn(Model $record) => auth()->user()->can('pendaftaran.update_status') && $record->status !== 'diterima' && $record->status !== 'ditolak')


            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
