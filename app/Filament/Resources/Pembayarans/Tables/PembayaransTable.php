<?php

namespace App\Filament\Resources\Pembayarans\Tables;

use App\Models\Jurusan;
use App\Models\Sekolah;
use Illuminate\Support\Facades\Mail;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PembayaransTable
{
    protected static function updateStatus(Model $record, string $status): void
    {
        $record->update(['status_pembayaran' => $status]);

        Notification::make()
            ->title('Status pembayaran diperbarui')
            ->body("Status berhasil diubah menjadi: {$status}")
            ->success()
            ->send();
    }

    protected static function markAsSuccess(Model $record, array $data): void
    {
        $catatan = $data['catatan'] ?? null;

        $record->update([
            'status_pembayaran'  => 'sukses',
            'tanggal_pembayaran' => now(),
            'catatan'            => $catatan,
            'verifikasi_oleh'    => auth()->id(),
            'verifikasi_tanggal' => now(),
        ]);

        // Sync status pendaftaran → pembayaran_lunas
        if ($record->pendaftaran) {
            $record->pendaftaran->update(['status' => 'pembayaran_lunas']);
        }

        // Kirim email notifikasi - pisah antara siswa/wali dan admin
        try {
            $pendaftaran = $record->load(['pendaftaran.siswa', 'pendaftaran.sekolah', 'pendaftaran.waliSiswas']);
            $pend        = $record->pendaftaran;
            $namaSiswa   = $pend->siswa->nama_siswa ?? '-';
            $kode        = $pend->kode_regis ?? '-';
            $sekolah     = $pend->sekolah->nama_sekolah ?? '-';
            $nominal     = 'Rp ' . number_format($record->nominal ?? 200000, 0, ',', '.');
            $verifikator = auth()->user()->name ?? 'Admin';
            $tglKonfirm  = now()->format('d F Y, H:i');

            // ── Generate PDF sekali, dipakai untuk email siswa saja
            $pdfOutput = null;
            try {
                $pend->load(['siswa', 'sekolah', 'jurusan', 'waliSiswas']);
                $namaMetode = ucfirst($record->metodePembayaran->nama_metode ?? 'manual');
                $ctrl       = app(\App\Http\Controllers\PembayaranController::class);
                $pdfOutput  = $ctrl->generatePdfLampiranPublic($pend, $record, $namaMetode);
            } catch (\Throwable $pe) {
                \Illuminate\Support\Facades\Log::warning('PDF generate gagal di approval: ' . $pe->getMessage());
            }

            // ── 1. Email ke SISWA & WALI: konfirmasi lunas + PDF lampiran
            $emailSiswa = collect([$pend->siswa?->email ?? null])
                ->merge($pend->waliSiswas->pluck('email')->filter())
                ->unique()->filter()->values();

            if ($emailSiswa->isNotEmpty()) {
                $subjectSiswa = "Pembayaran Lunas - PPDB {$kode}";
                $bodySiswa    = "<!DOCTYPE html><html><head><meta charset='UTF-8'>
<style>body{font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:0}.wrap{max-width:580px;margin:30px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.1)}.hdr{background:#0f4c3a;color:#fff;padding:28px;text-align:center}.kode{font-size:24px;font-weight:700;font-family:monospace;letter-spacing:3px;background:rgba(255,255,255,.2);padding:8px 18px;border-radius:8px;display:inline-block;margin-top:10px}.body{padding:26px 28px}.row{display:table;width:100%;margin:7px 0;font-size:13px}.lbl{display:table-cell;color:#666;width:160px}.val{display:table-cell;font-weight:600;color:#111}.catatan{background:#f0fdf4;border-left:3px solid #0f4c3a;border-radius:6px;padding:12px 14px;margin:14px 0;font-size:13px}.ftr{background:#f9fafb;border-top:1px solid #eee;padding:14px 28px;text-align:center;color:#999;font-size:11px}</style>
</head><body><div class='wrap'>
<div class='hdr'><h1 style='margin:0;font-size:20px'>Pembayaran Lunas!</h1><p style='margin:6px 0 0;opacity:.8;font-size:13px'>PPDB Online - Yayasan Fatahillah</p><div class='kode'>{$kode}</div></div>
<div class='body'>
<p style='font-size:13px;margin:0 0 14px'>Halo <strong>{$namaSiswa}</strong>, pembayaran pendaftaran Anda telah <strong>dikonfirmasi lunas</strong> oleh admin.</p>
<div style='background:#f9fafb;border-radius:8px;padding:14px;margin-bottom:14px'>
<div class='row'><span class='lbl'>Nama Siswa</span><span class='val'>{$namaSiswa}</span></div>
<div class='row'><span class='lbl'>Nomor Pendaftaran</span><span class='val'>{$kode}</span></div>
<div class='row'><span class='lbl'>Sekolah Tujuan</span><span class='val'>{$sekolah}</span></div>
<div class='row'><span class='lbl'>Nominal</span><span class='val'>{$nominal}</span></div>
<div class='row'><span class='lbl'>Dikonfirmasi Oleh</span><span class='val'>{$verifikator}</span></div>
<div class='row'><span class='lbl'>Tanggal Konfirmasi</span><span class='val'>{$tglKonfirm}</span></div>
</div>" .
($catatan ? "<div class='catatan'><strong>Catatan Admin:</strong><br>{$catatan}</div>" : "") .
"<p style='font-size:13px;color:#555;margin-top:12px'>Dokumen formulir pendaftaran terlampir. Harap datang ke sekolah tujuan dengan membawa berkas asli.</p>
</div>
<div class='ftr'>Email ini dikirim otomatis oleh sistem PPDB. Mohon jangan balas email ini.</div>
</div></body></html>";

                foreach ($emailSiswa as $email) {
                    Mail::html($bodySiswa, function($msg) use ($email, $subjectSiswa, $pdfOutput, $kode) {
                        $msg->to($email)->subject($subjectSiswa)
                            ->from(config('mail.from.address', 'noreply@ppdbfatahillah.my.id'),
                                   config('mail.from.name', 'PPDB Yayasan Fatahillah'));
                        if ($pdfOutput) {
                            $msg->attachData($pdfOutput, "Formulir-Pendaftaran-{$kode}.pdf", ['mime' => 'application/pdf']);
                        }
                    });
                }
            }

            // ── 2. Email ke ADMIN: notifikasi ringkas bahwa pembayaran sudah dikonfirmasi
            $adminEmails = array_filter(array_map('trim', explode(',', env('ADMIN_EMAIL', ''))));
            if (!empty($adminEmails)) {
                $adminPanelUrl = config('app.url') . '/admin/pembayarans';
                $subjectAdmin  = "[LUNAS] {$namaSiswa} — {$kode}";
                $bodyAdmin     = "<!DOCTYPE html><html><head><meta charset='UTF-8'>
<style>body{font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:0}.wrap{max-width:560px;margin:30px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.1)}.hdr{background:#0f4c3a;color:#fff;padding:20px 24px;text-align:center}.body{padding:20px 24px}.row{display:table;width:100%;margin:6px 0;font-size:13px}.lbl{display:table-cell;color:#666;width:150px}.val{display:table-cell;font-weight:600;color:#111}.btn{display:inline-block;background:#0f4c3a;color:#fff;text-decoration:none;padding:10px 22px;border-radius:8px;font-weight:700;font-size:13px;margin-top:14px}.ftr{background:#f9fafb;border-top:1px solid #eee;padding:12px 24px;text-align:center;color:#999;font-size:11px}</style>
</head><body><div class='wrap'>
<div class='hdr'><h1 style='margin:0;font-size:17px'>Pembayaran Dikonfirmasi Lunas</h1><p style='margin:4px 0 0;opacity:.85;font-size:12px'>Dikonfirmasi oleh: {$verifikator}</p></div>
<div class='body'>
<div class='row'><span class='lbl'>Nama Siswa</span><span class='val'>{$namaSiswa}</span></div>
<div class='row'><span class='lbl'>Kode Pendaftaran</span><span class='val' style='font-family:monospace'>{$kode}</span></div>
<div class='row'><span class='lbl'>Sekolah Tujuan</span><span class='val'>{$sekolah}</span></div>
<div class='row'><span class='lbl'>Nominal</span><span class='val'>{$nominal}</span></div>
<div class='row'><span class='lbl'>Tanggal Konfirmasi</span><span class='val'>{$tglKonfirm}</span></div>" .
($catatan ? "<div class='row'><span class='lbl'>Catatan</span><span class='val'>{$catatan}</span></div>" : "") .
"<a href='{$adminPanelUrl}' class='btn' style='color:#fff !important'>Lihat di Panel Admin</a>
</div>
<div class='ftr'>Notifikasi otomatis sistem PPDB. Hanya admin yang menerima ini.</div>
</div></body></html>";

                foreach ($adminEmails as $email) {
                    Mail::html($bodyAdmin, fn($msg) => $msg->to($email)->subject($subjectAdmin)
                        ->from(config('mail.from.address', 'noreply@ppdbfatahillah.my.id'),
                               config('mail.from.name', 'PPDB Yayasan Fatahillah')));
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Email konfirmasi lunas gagal: ' . $e->getMessage());
        }

        Notification::make()->title('Pembayaran dikonfirmasi lunas ✓')->success()->send();
    }

    public static function makrAsFailed(Model $record): void
    {
        $record->update([
            'status_pembayaran'  => 'gagal',
            'verifikasi_oleh'    => auth()->id(),
            'verifikasi_tanggal' => now(),
        ]);

        Notification::make()->title('Pembayaran gagal')->danger()->send();
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('pendaftaran.kode_regis')
                    ->label('Kode Registrasi')
                    ->searchable(),
                TextColumn::make('pendaftaran.nama_siswa')
                    ->label('Calon Murid')
                    ->searchable(),
                TextColumn::make('nominal')
                    ->numeric()
                    ->prefix('Rp.')
                    ->sortable(),
                TextColumn::make('metodePembayaran.nama_metode')
                    ->label('Pembayaran')
                    ->badge()
                    ->searchable(),
                BadgeColumn::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->colors([
                        'info'      => 'menunggu_verifikasi',
                        'secondary' => 'pending',
                        'danger'    => 'gagal',
                        'warning'   => 'kadaluarsa',
                        'success'   => 'sukses',
                    ]),
                TextColumn::make('tanggal_pembayaran')
                    ->label('Tanggal Pembayaran')
                    ->date()
                    ->sortable(),
                TextColumn::make('verifikator.name')
                    ->label('Diverifikasi Oleh')
                    ->searchable(),
            ])

            // ── FILTERS ──────────────────────────────────────────────────────
            ->filters([
                Filter::make('tanggal_pembayaran')
                    ->label('Rentang Tanggal Pembayaran')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('dari')
                            ->label('Dari Tanggal')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Semua tanggal'),
                        \Filament\Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai Tanggal')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Semua tanggal'),
                    ])
                    ->columns(2)
                    ->columnSpan(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['dari'],   fn($q) => $q->whereDate('tanggal_pembayaran', '>=', $data['dari']))
                            ->when($data['sampai'], fn($q) => $q->whereDate('tanggal_pembayaran', '<=', $data['sampai']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $i = [];
                        if ($data['dari'])   $i[] = 'Dari: '   . Carbon::parse($data['dari'])->format('d/m/Y');
                        if ($data['sampai']) $i[] = 'Sampai: ' . Carbon::parse($data['sampai'])->format('d/m/Y');
                        return $i;
                    }),

                SelectFilter::make('status_pembayaran')
                    ->label('Status')
                    ->options([
                        'pending'             => 'Menunggu Bayar',
                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                        'sukses'              => 'Lunas',
                        'gagal'               => 'Gagal',
                        'kadaluarsa'          => 'Kadaluarsa',
                    ])
                    ->placeholder('Semua Status')
                    ->columnSpan(1),

                SelectFilter::make('sekolah_id')
                    ->label('Sekolah')
                    ->options(function () {
                        $user = auth()->user();
                        if (!$user->hasAnyRole(['superadmin', 'admin_yayasan'])) {
                            $sid = $user->adminSekolah?->sekolah_id;
                            return $sid
                                ? [$sid => Sekolah::find($sid)?->nama_sekolah ?? '-']
                                : [];
                        }
                        return ['' => 'Semua Sekolah'] + Sekolah::orderBy('nama_sekolah')->pluck('nama_sekolah', 'id')->toArray();
                    })
                    ->placeholder('Semua Sekolah')
                    ->searchable()
                    ->columnSpan(1)
                    ->query(fn(Builder $query, array $data) =>
                        $query->when(
                            $data['value'] ?? null,
                            fn($q) => $q->whereHas('pendaftaran', fn($pq) => $pq->where('sekolah_id', $data['value']))
                        )
                    ),

                SelectFilter::make('jurusan_id')
                    ->label('Jurusan')
                    ->options(function () {
                        $user = auth()->user();
                        $q    = Jurusan::orderBy('nama_jurusan');
                        if (!$user->hasAnyRole(['superadmin', 'admin_yayasan'])) {
                            $sid = $user->adminSekolah?->sekolah_id;
                            if ($sid) $q->where('sekolah_id', $sid);
                        }
                        return ['' => 'Semua Jurusan'] + $q->pluck('nama_jurusan', 'id')->toArray();
                    })
                    ->placeholder('Semua Jurusan')
                    ->searchable()
                    ->columnSpan(1)
                    ->query(fn(Builder $query, array $data) =>
                        $query->when(
                            $data['value'] ?? null,
                            fn($q) => $q->whereHas('pendaftaran', fn($pq) => $pq->where('jurusan_id', $data['value']))
                        )
                    ),
            ])
            ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(6)

            // ── RECORD ACTIONS ────────────────────────────────────────────────
            ->recordActions([
                ActionGroup::make([
                    Action::make('menunggu_verifikasi')
                        ->label('Menunggu Verifikasi')
                        ->color(Color::Blue)
                        ->icon(Heroicon::ExclamationCircle)
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Perubahan Status')
                        ->modalDescription('Ubah status pembayaran menjadi Menunggu Verifikasi?')
                        ->modalSubmitActionLabel('Ya, Ubah Status')
                        ->action(fn(Model $record) => self::updateStatus($record, 'menunggu_verifikasi')),

                    Action::make('sukses')
                        ->label('Konfirmasi Lunas')
                        ->color(Color::Green)
                        ->icon(Heroicon::CheckBadge)
                        ->requiresConfirmation()
                        ->form([
                            Textarea::make('catatan')
                                ->label('Catatan Konfirmasi (opsional)')
                                ->placeholder('Contoh: Pembayaran via transfer BCA, sudah dikonfirmasi.')
                                ->rows(3),
                        ])
                        ->modalHeading('Konfirmasi Pembayaran Lunas')
                        ->modalDescription('Konfirmasi bahwa pembayaran telah diterima. Email notifikasi akan dikirim ke siswa.')
                        ->modalSubmitActionLabel('✓ Ya, Konfirmasi Lunas')
                        ->action(function (Model $record, array $data) {
                            self::markAsSuccess($record, $data);
                        }),

                    Action::make('gagal')
                        ->label('Tandai Gagal')
                        ->color(Color::Red)
                        ->icon(Heroicon::ExclamationTriangle)
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Pembayaran Gagal')
                        ->modalDescription('Apakah Anda yakin ingin menandai pembayaran ini sebagai gagal?')
                        ->modalSubmitActionLabel('Ya, Tandai Gagal')
                        ->action(fn(Model $record) => self::makrAsFailed($record)),
                ])
                    ->label('Konfirmasi')
                    ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                    ->button()
                    ->color(Color::Green)
                    ->visible(fn(Model $record) =>
                        // Superadmin read-only untuk pembayaran
                        !auth()->user()->hasRole('superadmin')
                        && auth()->user()->can('pembayaran.verify')
                        && in_array($record->status_pembayaran, ['pending', 'menunggu_verifikasi'])
                        // Admin sekolah hanya bisa verifikasi pembayaran sesuai sekolahnya
                        && (
                            auth()->user()->hasRole(['admin_yayasan'])
                            || auth()->user()->adminSekolah?->sekolah_id === $record->pendaftaran?->sekolah_id
                        )
                    ),
            ])

            // ── TOOLBAR ACTIONS
            // Export Excel & PDF dipindah ke halaman Laporan (Report > Laporan Pembayaran)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
