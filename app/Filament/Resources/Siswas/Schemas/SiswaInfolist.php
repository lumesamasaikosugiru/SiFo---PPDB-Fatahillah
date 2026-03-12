<?php

namespace App\Filament\Resources\Siswas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SiswaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Detail Calon Siswa')
                    ->schema([
                        TextEntry::make('nisn')
                            ->label('NISN'),
                        TextEntry::make('nama_siswa')
                            ->label('Nama Calon Murid'),
                        TextEntry::make('jk')
                            ->label('Jenis Kelamin')
                            ->badge(),
                        TextEntry::make('phone')
                            ->label('Kontak'),
                        TextEntry::make('email')
                            ->label('Email address')
                            ->label('Alamat Email'),
                        TextEntry::make('agama')
                            ->badge(),
                        TextEntry::make('tempat_lahir')
                            ->label('Tempat Lahir'),
                        TextEntry::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->date(),
                        TextEntry::make('asal_sekolah')
                            ->label('Asal Sekolah'),
                        TextEntry::make('tahun_lulus')
                            ->label('Tahun Lulus')
                            ->numeric(),
                        TextEntry::make('nomor_ijazah')
                            ->label('Nomor Ijazah'),

                    ])
                    ->columns(5)
                    ->columnSpan(5),

                Fieldset::make('Lain-lain')
                    ->schema([
                        TextEntry::make('pendaftaran.id')
                            ->label('ID Pendaftaran')
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),

                    ])
                    ->columns(1)
                    ->columnSpan(1),

                Section::make('Info Pendaftaran')
                    ->schema([
                        TextEntry::make('pendaftaran.nama_sekolah')
                            ->label('Tujuan Sekolah'),
                        TextEntry::make('pendaftaran.nama_jurusan')
                            ->label('Jurusan yang dipilih')
                    ])
                    ->columns(3)
                    ->columnSpan(5),
            ])
            ->columns(6);
    }
}
