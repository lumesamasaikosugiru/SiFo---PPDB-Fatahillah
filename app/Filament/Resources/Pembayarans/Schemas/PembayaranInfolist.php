<?php

namespace App\Filament\Resources\Pembayarans\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PembayaranInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Bukti Bayar')
                    ->schema([
                        TextEntry::make('nominal')
                            ->prefix('IDR.')
                            ->numeric(),
                        TextEntry::make('pendaftaran.id')
                            ->label('ID Pembayaran')
                            ->placeholder('-'),
                        TextEntry::make('catatan')
                            ->label('Catatan'),
                        ImageEntry::make('proof_path')
                            ->imageHeight(250)
                            ->disk('public')
                            ->placeholder('belum dibayar')
                            ->hiddenLabel(),
                    ])
                    ->icon(Heroicon::DocumentCheck)
                    ->columns(2)
                    ->columnSpan(2),

                Fieldset::make('Detail Pembayaran')
                    ->schema([

                        TextEntry::make('metodePembayaran.nama_metode')
                            ->label('Metode Bayar')
                            ->badge()
                            ->placeholder('-'),
                        TextEntry::make('status_pembayaran')
                            ->badge(),
                        TextEntry::make('order_id')
                            ->placeholder('-'),
                        TextEntry::make('pendaftaran.kode_regis')
                            ->placeholder('-'),
                        TextEntry::make('tanggal_pembayaran')
                            ->date(),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                    ])
                    ->columnSpan(2),

                Section::make('Info Pendaftar')
                    ->schema([
                        TextEntry::make('nama_siswa')
                            ->label('Nama Calon Murid'),
                        TextEntry::make('nama_sekolah')
                            ->label('Sekolah'),
                        TextEntry::make('nama_jurusan')
                            ->label('Jurusan'),
                        TextEntry::make('asal_sekolah')
                            ->label('Asal Sekolah'),
                    ])
                    ->icon(Heroicon::ExclamationCircle)
                    ->columns(4)
                    ->columnSpan(4),
            ])
            ->columns(4);
    }
}
