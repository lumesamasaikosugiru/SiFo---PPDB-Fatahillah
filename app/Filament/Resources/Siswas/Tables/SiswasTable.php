<?php

namespace App\Filament\Resources\Siswas\Tables;

use App\Models\Jurusan;
use App\Models\Sekolah;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class SiswasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable(),
                TextColumn::make('nama_siswa')
                    ->label('Nama Calon Murid')
                    ->searchable(),
                TextColumn::make('jk')
                    ->label('Jenis Kelamin')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'laki_laki'  => 'info',
                        'Laki-Laki'  => 'info',
                        'perempuan'  => 'success',
                        'Perempuan'  => 'success',
                        default      => 'gray',
                    }),
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
                TextColumn::make('pendaftaran.sekolah.nama_sekolah')
                    ->label('Sekolah Tujuan')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('pendaftaran.jurusan.nama_jurusan')
                    ->label('Jurusan')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('pendaftaran.jalur_pendaftaran')
                    ->label('Jalur Pendaftaran')
                    ->badge()
                    ->sortable(),
                TextColumn::make('pendaftaran.status')
                    ->label('Status Pendaftaran')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'diverifikasi'        => 'info',
                        'diterima'            => 'success',
                        'ditolak'             => 'danger',
                        'pembayaran_lunas'    => 'secondary',
                        'menunggu_pembayaran' => 'warning',
                        'selesai'             => 'gray',
                        default               => 'primary',
                    })
                    ->sortable(),
            ])

            ->filters([
                // ── 1. Filter Jenis Kelamin ────────────────────────────────
                SelectFilter::make('jk')
                    ->label('Jenis Kelamin')
                    ->options([
                        'laki_laki' => 'Laki-Laki',
                        'perempuan' => 'Perempuan',
                    ])
                    ->placeholder('Semua Jenis Kelamin')
                    ->searchable()
                    ->columnSpan(1),

                // ── 2. Filter Sekolah (via relasi pendaftaran) ─────────────
                SelectFilter::make('sekolah_id')
                    ->label('Sekolah Tujuan')
                    ->options(function () {
                        $user = auth()->user();
                        if (!$user->hasAnyRole(['superadmin', 'admin_yayasan'])) {
                            $sid  = $user->adminSekolah?->sekolah_id;
                            return $sid
                                ? [$sid => Sekolah::find($sid)?->nama_sekolah ?? '-']
                                : [];
                        }
                        return ['' => 'Semua Sekolah']
                            + Sekolah::orderBy('nama_sekolah')->pluck('nama_sekolah', 'id')->toArray();
                    })
                    ->placeholder('Semua Sekolah')
                    ->searchable()
                    ->query(fn (Builder $query, array $data): Builder =>
                        $data['value']
                            ? $query->whereHas('pendaftaran', fn ($q) => $q->where('sekolah_id', $data['value']))
                            : $query
                    )
                    ->columnSpan(1),

                // ── 3. Filter Jurusan (via relasi pendaftaran) ─────────────
                SelectFilter::make('jurusan_id')
                    ->label('Jurusan')
                    ->options(function () {
                        $user = auth()->user();
                        $q    = Jurusan::orderBy('nama_jurusan');
                        if (!$user->hasAnyRole(['superadmin', 'admin_yayasan'])) {
                            $sid = $user->adminSekolah?->sekolah_id;
                            if ($sid) $q->where('sekolah_id', $sid);
                        }
                        return ['' => 'Semua Jurusan']
                            + $q->pluck('nama_jurusan', 'id')->toArray();
                    })
                    ->placeholder('Semua Jurusan')
                    ->searchable()
                    ->query(fn (Builder $query, array $data): Builder =>
                        $data['value']
                            ? $query->whereHas('pendaftaran', fn ($q) => $q->where('jurusan_id', $data['value']))
                            : $query
                    )
                    ->columnSpan(1),

                // ── 4. Filter Tahun Lulus ──────────────────────────────────
                SelectFilter::make('tahun_lulus')
                    ->label('Tahun Lulus')
                    ->options(function () {
                        $years = \App\Models\Siswa::query()
                            ->whereNotNull('tahun_lulus')
                            ->distinct()
                            ->orderByDesc('tahun_lulus')
                            ->pluck('tahun_lulus')
                            ->mapWithKeys(fn ($y) => [$y => $y])
                            ->toArray();
                        return ['' => 'Semua Tahun'] + $years;
                    })
                    ->placeholder('Semua Tahun Lulus')
                    ->searchable()
                    ->columnSpan(1),

                // ── 5. Filter Jalur Pendaftaran (via relasi pendaftaran) ───
                SelectFilter::make('jalur_pendaftaran')
                    ->label('Jalur Pendaftaran')
                    ->options([
                        'reguler'  => 'Reguler',
                        'prestasi' => 'Prestasi',
                        'afirmasi' => 'Afirmasi',
                        'pindahan' => 'Pindahan',
                    ])
                    ->placeholder('Semua Jalur')
                    ->searchable()
                    ->query(fn (Builder $query, array $data): Builder =>
                        $data['value']
                            ? $query->whereHas('pendaftaran', fn ($q) => $q->where('jalur_pendaftaran', $data['value']))
                            : $query
                    )
                    ->columnSpan(1),
            ])
            ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(5)

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
