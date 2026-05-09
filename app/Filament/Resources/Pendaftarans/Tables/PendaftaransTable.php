<?php

namespace App\Filament\Resources\Pendaftarans\Tables;

use App\Models\Jurusan;
use App\Models\Sekolah;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PendaftaransTable
{
    // ─────────────────────────────────────────────────────────────────────────
    // STATUS UPDATE + EMAIL TRIGGER
    // ─────────────────────────────────────────────────────────────────────────

    protected static function updateStatus(Model $record, string $status, ?string $alasan = null): void
    {
        $record->update([
            'status'            => $status,
            'diverifikasi_oleh' => auth()->id(),
        ]);

        // Kirim email notifikasi ke siswa untuk status diterima / ditolak
        if (in_array($status, ['diterima', 'ditolak'])) {
            static::sendStatusEmail($record, $status, $alasan);
        }

        Notification::make()
            ->title('Status pendaftaran diperbarui')
            ->body("Status berhasil diubah menjadi: {$status}")
            ->success()
            ->send();
    }

    /**
     * Kirim email notifikasi ke siswa ketika status berubah jadi diterima/ditolak.
     */
    protected static function sendStatusEmail(Model $record, string $status, ?string $alasan = null): void
    {
        try {
            $record->loadMissing(['siswa', 'sekolah', 'jurusan', 'waliSiswas']);

            $siswa      = $record->siswa;
            $namaSiswa  = $siswa?->nama_siswa   ?? 'Calon Siswa';
            $kode       = $record->kode_regis   ?? '-';
            $namaSekolah= $record->sekolah?->nama_sekolah ?? '-';
            $namaJurusan= $record->jurusan?->nama_jurusan ?? '-';
            $verifikator= auth()->user()?->name ?? 'Admin';
            $tglUpdate  = now()->format('d F Y, H:i');
            $isDiterima = $status === 'diterima';

            // Kumpulkan semua email penerima: siswa + wali siswa
            $emailList = collect([$siswa?->email])
                ->merge($record->waliSiswas->pluck('email')->filter())
                ->unique()
                ->filter()
                ->values();

            if ($emailList->isEmpty()) {
                Log::info("PendaftaransTable: tidak ada email untuk kode {$kode}");
                return;
            }

            if ($isDiterima) {
                $subject   = "🎉 Selamat! Pendaftaran Anda Diterima — {$kode}";
                $badgeColor = '#16a34a';
                $badgeText  = 'DITERIMA';
                $headline   = 'Selamat, Anda Diterima!';
                $subline    = "Pendaftaran Anda di <strong>{$namaSekolah}</strong> telah <strong>diterima</strong> oleh panitia PPDB.";
                $bodyExtra  = "
                    <p style='font-size:13px;color:#374151;margin:14px 0 6px'>
                        Langkah selanjutnya:
                    </p>
                    <ol style='font-size:13px;color:#374151;margin:0;padding-left:18px;line-height:1.8'>
                        <li>Harap menyelesaikan <strong>pembayaran biaya pendaftaran</strong> sesuai petunjuk dari sekolah.</li>
                        <li>Datang ke sekolah tujuan membawa berkas asli untuk verifikasi dokumen.</li>
                        <li>Pantau status pendaftaran Anda secara berkala melalui website PPDB.</li>
                    </ol>";
                $footerNote = 'Hubungi pihak sekolah jika ada pertanyaan lebih lanjut.';
            } else {
                $subject    = "Informasi Status Pendaftaran — {$kode}";
                $badgeColor = '#dc2626';
                $badgeText  = 'TIDAK DITERIMA';
                $headline   = 'Informasi Hasil Seleksi';
                $subline    = "Dengan hormat, kami informasikan bahwa pendaftaran Anda di <strong>{$namaSekolah}</strong> <strong>belum dapat kami terima</strong> pada periode ini.";
                $bodyExtra  = $alasan
                    ? "<div style='background:#fef2f2;border-left:3px solid #dc2626;border-radius:6px;padding:12px 14px;margin:14px 0;font-size:13px;color:#7f1d1d'>
                           <strong>Keterangan:</strong><br>{$alasan}
                       </div>"
                    : "<p style='font-size:13px;color:#374151;margin:14px 0'>
                           Anda dapat mencoba mendaftar di sekolah atau jurusan lain yang sesuai.
                       </p>";
                $footerNote = 'Terima kasih telah mendaftar di PPDB Yayasan Fatahillah Cilegon.';
            }

            $html = "<!DOCTYPE html><html><head><meta charset='UTF-8'>
<style>
body{font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:0}
.wrap{max-width:580px;margin:30px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.1)}
.hdr{background:{$badgeColor};color:#fff;padding:28px;text-align:center}
.kode{font-size:22px;font-weight:700;font-family:monospace;letter-spacing:3px;background:rgba(255,255,255,.2);padding:8px 18px;border-radius:8px;display:inline-block;margin-top:10px}
.badge-status{display:inline-block;background:rgba(255,255,255,.25);color:#fff;font-weight:800;font-size:13px;letter-spacing:1px;padding:4px 14px;border-radius:20px;margin-top:8px}
.body{padding:24px 28px}
.info-box{background:#f9fafb;border-radius:8px;padding:14px;margin:14px 0}
.row{display:table;width:100%;margin:6px 0;font-size:13px}
.lbl{display:table-cell;color:#6b7280;width:150px}
.val{display:table-cell;font-weight:600;color:#111827}
.ftr{background:#f9fafb;border-top:1px solid #e5e7eb;padding:12px 28px;text-align:center;color:#9ca3af;font-size:11px}
</style>
</head><body><div class='wrap'>
<div class='hdr'>
  <h1 style='margin:0;font-size:20px'>{$headline}</h1>
  <p style='margin:6px 0 0;opacity:.85;font-size:13px'>PPDB Online — Yayasan Fatahillah Cilegon</p>
  <div class='kode'>{$kode}</div>
  <div class='badge-status'>{$badgeText}</div>
</div>
<div class='body'>
  <p style='font-size:13px;color:#374151;margin:0 0 14px'>Halo <strong>{$namaSiswa}</strong>,</p>
  <p style='font-size:13px;color:#374151;margin:0 0 14px'>{$subline}</p>
  <div class='info-box'>
    <div class='row'><span class='lbl'>Nama Siswa</span><span class='val'>{$namaSiswa}</span></div>
    <div class='row'><span class='lbl'>No. Pendaftaran</span><span class='val'>{$kode}</span></div>
    <div class='row'><span class='lbl'>Sekolah Tujuan</span><span class='val'>{$namaSekolah}</span></div>
    <div class='row'><span class='lbl'>Jurusan</span><span class='val'>{$namaJurusan}</span></div>
    <div class='row'><span class='lbl'>Diproses Oleh</span><span class='val'>{$verifikator}</span></div>
    <div class='row'><span class='lbl'>Tanggal Update</span><span class='val'>{$tglUpdate}</span></div>
  </div>
  {$bodyExtra}
  <p style='font-size:12px;color:#9ca3af;margin-top:16px'>{$footerNote}</p>
</div>
<div class='ftr'>Email ini dikirim otomatis oleh sistem PPDB. Mohon jangan balas email ini.</div>
</div></body></html>";

            foreach ($emailList as $emailAddr) {
                Mail::html($html, function ($msg) use ($emailAddr, $subject) {
                    $msg->to($emailAddr)
                        ->subject($subject)
                        ->from(
                            config('mail.from.address', 'noreply@ppdbfatahillah.my.id'),
                            config('mail.from.name', 'PPDB Yayasan Fatahillah')
                        );
                });
            }

            Log::info("Email status '{$status}' terkirim ke: " . $emailList->implode(', ') . " — kode: {$kode}");

        } catch (\Throwable $e) {
            Log::warning("Email status pendaftaran gagal [{$status}] kode {$record->kode_regis}: " . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TABLE CONFIGURATION
    // ─────────────────────────────────────────────────────────────────────────

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('kode_regis')
                    ->label('Kode Registrasi')
                    ->searchable(),
                TextColumn::make('TahunAkademik.tahun')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('sekolah.nama_sekolah')
                    ->searchable(),
                TextColumn::make('siswa.asal_sekolah')
                    ->label('Asal Sekolah')
                    ->searchable(),
                TextColumn::make('jurusan.nama_jurusan')
                    ->searchable(),
                TextColumn::make('jalur_pendaftaran')
                    ->label('Jalur Pendaftaran')
                    ->badge(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'info'      => 'diverifikasi',
                        'success'   => 'diterima',
                        'danger'    => 'ditolak',
                        'secondary' => 'pembayaran_lunas',
                        'warning'   => 'menunggu_pembayaran',
                        'light'     => 'selesai',
                    ]),
                TextColumn::make('tanggal_submit')
                    ->label('Tanggal Submit')
                    ->date()
                    ->sortable(),
                TextColumn::make('userVerifikator.name')
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

            // ── FILTERS ──────────────────────────────────────────────────────
            ->filters([
                Filter::make('tanggal_submit')
                    ->label('Rentang Tanggal Submit')
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
                            ->when($data['dari'],   fn($q) => $q->whereDate('tanggal_submit', '>=', $data['dari']))
                            ->when($data['sampai'], fn($q) => $q->whereDate('tanggal_submit', '<=', $data['sampai']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $i = [];
                        if ($data['dari'])   $i[] = 'Dari: '   . Carbon::parse($data['dari'])->format('d/m/Y');
                        if ($data['sampai']) $i[] = 'Sampai: ' . Carbon::parse($data['sampai'])->format('d/m/Y');
                        return $i;
                    }),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'diproses'            => 'Diproses',
                        'diverifikasi'        => 'Diverifikasi',
                        'diterima'            => 'Diterima',
                        'ditolak'             => 'Ditolak',
                        'menunggu_pembayaran' => 'Menunggu Bayar',
                        'pembayaran_diproses' => 'Bayar Diproses',
                        'pembayaran_lunas'    => 'Bayar Lunas',
                        'selesai'             => 'Selesai',
                    ])
                    ->placeholder('Semua Status')
                    ->columnSpan(1),

                SelectFilter::make('sekolah_id')
                    ->label('Sekolah')
                    ->options(function () {
                        $user = auth()->user();
                        // Admin sekolah: hanya tampilkan sekolahnya sendiri
                        if (!$user->hasAnyRole(['superadmin', 'admin_yayasan'])) {
                            $sid = $user->adminSekolah?->sekolah_id;
                            return $sid
                                ? [Sekolah::find($sid)?->nama_sekolah ?? '-']
                                : [];
                        }
                        return ['' => 'Semua Sekolah'] + Sekolah::orderBy('nama_sekolah')->pluck('nama_sekolah', 'id')->toArray();
                    })
                    ->placeholder('Semua Sekolah')
                    ->searchable()
                    ->columnSpan(1),

                SelectFilter::make('jurusan_id')
                    ->label('Jurusan')
                    ->options(function () {
                        $user = auth()->user();
                        $q    = Jurusan::orderBy('nama_jurusan');
                        // Admin sekolah: hanya tampilkan jurusan sekolahnya
                        if (!$user->hasAnyRole(['superadmin', 'admin_yayasan'])) {
                            $sid = $user->adminSekolah?->sekolah_id;
                            if ($sid) $q->where('sekolah_id', $sid);
                        }
                        return ['' => 'Semua Jurusan'] + $q->pluck('nama_jurusan', 'id')->toArray();
                    })
                    ->placeholder('Semua Jurusan')
                    ->searchable()
                    ->columnSpan(1),
            ])
            ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(6)

            // ── RECORD ACTIONS ────────────────────────────────────────────────
            ->recordActions([
                ActionGroup::make([
                    Action::make('diproses')
                        ->label('Diproses')
                        ->color(Color::Amber)
                        ->icon(Heroicon::DocumentArrowUp)
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Perubahan Status')
                        ->modalDescription('Ubah status pendaftaran menjadi Diproses?')
                        ->modalSubmitActionLabel('Ya, Konfirmasi')
                        ->action(fn(Model $record) => self::updateStatus($record, 'diproses')),

                    Action::make('diverifikasi')
                        ->label('Diverifikasi')
                        ->color(Color::Blue)
                        ->icon(Heroicon::CheckBadge)
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Perubahan Status')
                        ->modalDescription('Ubah status pendaftaran menjadi Diverifikasi?')
                        ->modalSubmitActionLabel('Ya, Konfirmasi')
                        ->action(fn(Model $record) => self::updateStatus($record, 'diverifikasi')),

                    // ── DITERIMA: tambah form input catatan opsional ──────────
                    Action::make('diterima')
                        ->label('Diterima')
                        ->color(Color::Green)
                        ->icon(Heroicon::CheckBadge)
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Pendaftaran Diterima')
                        ->modalDescription('Siswa akan mendapatkan notifikasi email bahwa pendaftarannya DITERIMA.')
                        ->modalSubmitActionLabel('✓ Ya, Terima & Kirim Email')
                        ->action(fn(Model $record) => self::updateStatus($record, 'diterima')),

                    // ── DITOLAK: wajib isi alasan penolakan ──────────────────
                    Action::make('ditolak')
                        ->label('Ditolak')
                        ->color(Color::Red)
                        ->icon(Heroicon::ExclamationTriangle)
                        ->modalHeading('Konfirmasi Penolakan Pendaftaran')
                        ->modalDescription('Siswa akan mendapatkan notifikasi email bahwa pendaftarannya TIDAK DITERIMA.')
                        ->modalSubmitActionLabel('✗ Ya, Tolak & Kirim Email')
                        ->form([
                            \Filament\Forms\Components\Textarea::make('alasan_penolakan')
                                ->label('Alasan Penolakan (opsional)')
                                ->placeholder('Contoh: Kuota jurusan sudah penuh, dokumen tidak lengkap, dst.')
                                ->rows(3),
                        ])
                        ->action(function (Model $record, array $data) {
                            self::updateStatus($record, 'ditolak', $data['alasan_penolakan'] ?? null);
                        }),

                    Action::make('menunggu_pembayaran')
                        ->label('Menunggu Pembayaran')
                        ->color(Color::Orange)
                        ->icon(Heroicon::ExclamationCircle)
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Perubahan Status')
                        ->modalDescription('Ubah status pendaftaran menjadi Menunggu Pembayaran?')
                        ->modalSubmitActionLabel('Ya, Konfirmasi')
                        ->action(fn(Model $record) => self::updateStatus($record, 'menunggu_pembayaran')),

                    Action::make('pembayaran_diproses')
                        ->label('Pembayaran Diproses')
                        ->color(Color::Purple)
                        ->icon(Heroicon::OutlinedCreditCard)
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Perubahan Status')
                        ->modalDescription('Ubah status pendaftaran menjadi Pembayaran Diproses?')
                        ->modalSubmitActionLabel('Ya, Konfirmasi')
                        ->action(fn(Model $record) => self::updateStatus($record, 'pembayaran_diproses')),

                    Action::make('pembayaran_lunas')
                        ->label('Pembayaran Lunas')
                        ->color(Color::Teal)
                        ->icon(Heroicon::CheckBadge)
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Perubahan Status')
                        ->modalDescription('Ubah status pendaftaran menjadi Pembayaran Lunas?')
                        ->modalSubmitActionLabel('Ya, Konfirmasi')
                        ->action(fn(Model $record) => self::updateStatus($record, 'pembayaran_lunas')),

                    Action::make('selesai')
                        ->label('Selesai')
                        ->color(Color::Green)
                        ->icon(Heroicon::CheckBadge)
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Perubahan Status')
                        ->modalDescription('Ubah status pendaftaran menjadi Selesai?')
                        ->modalSubmitActionLabel('Ya, Konfirmasi')
                        ->action(fn(Model $record) => self::updateStatus($record, 'selesai')),
                ])
                    ->label('Konfirmasi')
                    ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                    ->button()
                    ->color(Color::Green)
                    ->visible(fn(Model $record) =>
                        !auth()->user()->hasRole('superadmin')
                        && auth()->user()->can('pendaftaran.update_status')
                        && $record->status !== 'selesai'
                        && (
                            auth()->user()->hasRole(['admin_yayasan'])
                            || auth()->user()->adminSekolah?->sekolah_id === $record->sekolah_id
                        )
                    ),
            ])

            // ── TOOLBAR ACTIONS
            // Export Excel & PDF dipindah ke halaman Laporan (Report > Laporan Pendaftaran)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
