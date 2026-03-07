<?php

namespace App\Filament\Resources\Pembayarans\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PembayaransTable
{

    protected static function updateStatus(Model $record, string $status): void
    {
        $record->update([
            'status_pembayaran' => $status,
        ]);

        Notification::make()
            ->title('Status pembayaran diperbarui')
            ->body("Status berhasil diubah menjadi: {$status}")
            ->success()
            ->send();
    }

    protected static function markAsSuccess(Model $record, array $data): void
    {
        if (
            in_array(optional($record->metodePembayaran)->slug, ['cash', 'transfer']) && empty($data['proof_path'])
        ) {
            throw new \Exception('Bukti pembayaran wajib diupload.');
        }
        $record->update([
            'status_pembayaran' => 'sukses',
            'proof_path' => $data['proof_path'] ?? null,
            'tanggal_pembayaran' => now(),
            'verifikasi_oleh' => auth()->id(),
            'verifikasi_tanggal' => now(),
        ]);

    }

    public static function makrAsFailed(Model $record): void
    {

        $record->update([
            'status_pembayaran' => 'gagal',
            'verifikasi_oleh' => auth()->id(),
            'verifikasi_tanggal' => now(),
        ]);

        Notification::make()
            ->title('Pembayaran gagal')
            ->danger()
            ->send();
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('metodePembayaran.nama_metode')
                    ->label('Pembayaran')
                    ->searchable(),
                TextColumn::make('pendaftaran.kode_regis')
                    ->label('Kode Registrasi')
                    ->searchable(),
                TextColumn::make('nominal')
                    ->numeric()
                    ->prefix('Rp.')
                    ->sortable(),
                TextColumn::make('order_id')
                    ->label('ID Order')
                    ->searchable(),
                BadgeColumn::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->colors([
                        'info' => 'menunggu_verifikasi',
                        'secondary' => 'pending',
                        'danger' => 'gagal',
                        'warning' => 'kadaluarsa',
                    ]),
                TextColumn::make('tanggal_pembayaran')
                    ->label('Tanggal Pembayaran')
                    ->date()
                    ->sortable(),
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
                    Action::make('menunggu_verifikasi')
                        ->label('Menunggu Verifikasi')
                        ->color(Color::Green)
                        ->icon(Heroicon::DocumentText)
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Perubahan Status')
                        ->modalDescription('Apakah anda yakin ingin mengubah Status Pendaftaran')
                        ->modalSubmitActionLabel('Ya, Ubah Status')
                        ->action(fn(Model $record) => self::updateStatus($record, 'menunggu_verifikasi')),

                    Action::make('sukses')
                        ->label('Sukses')
                        ->color(Color::Green)
                        ->icon(Heroicon::CheckBadge)
                        ->requiresConfirmation()
                        ->form([
                            FileUpload::make('proof_path')
                                ->label('Upload foto kwintansi')
                                ->image()
                                ->disk('public')
                                ->directory('bukti_bayar')
                                ->visibility('public')
                                ->required(fn(Model $record) => in_array(optional($record->metodePembayaran)->slug, ['cash', 'transfer']))
                        ])
                        ->modalHeading('Konfirmasi Pembayaran')
                        ->modalDescription('Pastikan pembayaran benar & Bukti Pembayaran telah diupload')
                        ->modalSubmitActionLabel('Ya, Konfirmasi')
                        ->action(function (Model $record, array $data) {
                            self::markAsSuccess($record, $data);
                        }),

                    Action::make('gagal')
                        ->label('Gagal')
                        ->color(Color::Green)
                        ->icon(Heroicon::ExclamationTriangle)
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Perubahan Status')
                        ->modalDescription('Apakah anda yakin ingin mengubah Status Pendaftaran')
                        ->modalSubmitActionLabel('Ya, Ubah Status')
                        ->action(fn(Model $record) => self::makrAsFailed($record))

                ])
                    ->label('Ubah Status')
                    ->icon(Heroicon::PencilSquare)
                    ->visible(fn(Model $record) => auth()->user()->can('pembayaran.verify') && in_array($record->status_pembayaran, ['pending', 'menunggu_verifikasi']))
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
