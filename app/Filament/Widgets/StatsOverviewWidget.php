<?php

namespace App\Filament\Widgets;

use App\Models\Jurusan;
use App\Models\Pendaftaran;
use App\Models\Pembayaran;
use App\Models\Sekolah;
use App\Models\Siswa;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort    = 0;
    protected static bool $isLazy  = false;

    /**
     * Base query pendaftaran sesuai role:
     * - superadmin & admin_yayasan: semua data
     * - admin_sekolah & kepala_sekolah: hanya sekolah sendiri
     */
    private function pendaftaranQuery()
    {
        $user = auth()->user();
        $q = Pendaftaran::query();

        if ($user->hasAnyRole(['superadmin', 'admin_yayasan'])) {
            return $q;
        }

        $sekolahId = $user->adminSekolah?->sekolah_id;
        return $sekolahId ? $q->where('sekolah_id', $sekolahId) : $q->whereRaw('0=1');
    }

    private function pembayaranQuery()
    {
        $user = auth()->user();
        $q = Pembayaran::query();

        if ($user->hasAnyRole(['superadmin', 'admin_yayasan'])) {
            return $q;
        }

        $sekolahId = $user->adminSekolah?->sekolah_id;
        if (!$sekolahId) return $q->whereRaw('0=1');

        return $q->whereHas('pendaftaran', fn($pq) => $pq->where('sekolah_id', $sekolahId));
    }

    protected function getStats(): array
    {
        $pq = $this->pendaftaranQuery();
        $bq = $this->pembayaranQuery();

        $totalPendaftar     = (clone $pq)->count();
        $diproses           = (clone $pq)->where('status', 'diproses')->count();
        $diverifikasi       = (clone $pq)->where('status', 'diverifikasi')->count();
        $diterima           = (clone $pq)->where('status', 'diterima')->count();
        $ditolak            = (clone $pq)->where('status', 'ditolak')->count();
        $menungguBayar      = (clone $pq)->where('status', 'menunggu_pembayaran')->count();
        $pembayaranDiproses = (clone $pq)->where('status', 'pembayaran_diproses')->count();
        $pembayaranLunas    = (clone $pq)->where('status', 'pembayaran_lunas')->count();
        $selesai            = (clone $pq)->where('status', 'selesai')->count();

        $menungguVerifikasi = (clone $bq)->where('status_pembayaran', 'menunggu_verifikasi')->count();
        $pembayaranSukses   = (clone $bq)->where('status_pembayaran', 'sukses')->count();

        // Data master (selalu global)
        // Scoped by role: admin sekolah hanya lihat data sekolahnya
        $user = auth()->user();
        if ($user->hasAnyRole(['superadmin', 'admin_yayasan'])) {
            $totalMurid   = Siswa::count();
            $totalSekolah = Sekolah::count();
            $totalJurusan = Jurusan::count();
        } else {
            $sekolahId    = $user->adminSekolah?->sekolah_id;
            // Total murid = hitung dari tabel pendaftarans (bukan Siswa->pendaftarans)
            $totalMurid   = $sekolahId
                ? \App\Models\Pendaftaran::where('sekolah_id', $sekolahId)->count()
                : 0;
            $totalSekolah = $sekolahId ? 1 : 0; // hanya 1 sekolah
            $totalJurusan = $sekolahId ? Jurusan::where('sekolah_id', $sekolahId)->count() : 0;
        }

        $user      = auth()->user();
        $isGlobal  = $user->hasAnyRole(['superadmin', 'admin_yayasan']);
        $scopeLabel = $isGlobal ? 'Semua Sekolah' : ('Sekolah: ' . ($user->adminSekolah?->sekolah?->nama_sekolah ?? '-'));

        return [
            Stat::make('Total Pendaftar', $totalPendaftar)
                ->description($scopeLabel)
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('primary'),

            Stat::make('Diproses', $diproses)
                ->description('Menunggu verifikasi admin')
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('warning'),

            Stat::make('Diverifikasi', $diverifikasi)
                ->description('Sudah diverifikasi')
                ->descriptionIcon('heroicon-o-document-check')
                ->color('info'),

            Stat::make('Diterima', $diterima)
                ->description('Pendaftar diterima')
                ->descriptionIcon('heroicon-o-check-badge')
                ->color('success'),

            Stat::make('Ditolak', $ditolak)
                ->description('Pendaftar ditolak')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Menunggu Pembayaran', $menungguBayar)
                ->description('Belum melakukan pembayaran')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('warning'),

            Stat::make('Bayar Diproses', $pembayaranDiproses)
                ->description('Pembayaran sedang diproses')
                ->descriptionIcon('heroicon-o-credit-card')
                ->color('info'),

            Stat::make('Menunggu Verifikasi Bayar', $menungguVerifikasi)
                ->description('Perlu dikonfirmasi admin')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Pembayaran Lunas', $pembayaranLunas)
                ->description('Dikonfirmasi lunas')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('Pendaftaran Selesai', $selesai)
                ->description('Proses selesai seluruhnya')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Total Murid', $totalMurid)
                ->description('Data siswa terdaftar')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('primary'),

            Stat::make('Jumlah Sekolah', $totalSekolah)
                ->description('Sekolah aktif di sistem')
                ->descriptionIcon('heroicon-o-building-office-2')
                ->color('primary'),
        ];
    }
}
