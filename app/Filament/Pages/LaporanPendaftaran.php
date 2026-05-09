<?php

namespace App\Filament\Pages;

use App\Models\Jurusan;
use App\Models\Pendaftaran;
use App\Models\Sekolah;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class LaporanPendaftaran extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.laporan-pendaftaran';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static ?string $navigationLabel               = 'Laporan Pendaftaran';
    protected static ?string $title                         = 'Laporan Rekap Pendaftaran';
    protected static ?int    $navigationSort                = 9;
    protected static string|UnitEnum|null $navigationGroup  = 'Report';
    protected static ?string $slug                          = 'laporan-pendaftaran';

    public ?string $filterDari      = null;
    public ?string $filterSampai    = null;
    public ?string $filterSekolahId = null;
    public ?string $filterJurusanId = null;
    public ?string $filterStatus    = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['superadmin', 'admin_yayasan', 'admin_sekolah', 'kepala_sekolah']) ?? false;
    }

    private function baseQuery(): Builder
    {
        $user = auth()->user();
        $q    = Pendaftaran::with(['siswa', 'sekolah', 'jurusan', 'pembayarans', 'tahunAkademik']);
        if (!$user->hasAnyRole(['superadmin', 'admin_yayasan'])) {
            $sid = $user->adminSekolah?->sekolah_id;
            $q   = $sid ? $q->where('sekolah_id', $sid) : $q->whereRaw('0=1');
        }
        return $q;
    }

    private function filteredQuery(): Builder
    {
        $q = $this->baseQuery();
        if ($this->filterDari)      $q->whereDate('tanggal_submit', '>=', $this->filterDari);
        if ($this->filterSampai)    $q->whereDate('tanggal_submit', '<=', $this->filterSampai);
        if ($this->filterSekolahId) $q->where('sekolah_id', $this->filterSekolahId);
        if ($this->filterJurusanId) $q->where('jurusan_id', $this->filterJurusanId);
        if ($this->filterStatus)    $q->where('status', $this->filterStatus);
        return $q->orderBy('tanggal_submit', 'desc');
    }

    public function getSummary(): array
    {
        $q = $this->filteredQuery();
        return [
            'total'         => (clone $q)->count(),
            'diproses'      => (clone $q)->where('status', 'diproses')->count(),
            'diverifikasi'  => (clone $q)->where('status', 'diverifikasi')->count(),
            'diterima'      => (clone $q)->where('status', 'diterima')->count(),
            'ditolak'       => (clone $q)->where('status', 'ditolak')->count(),
            'blmBayar'      => (clone $q)->where('status', 'menunggu_pembayaran')->count(),
            'prosesBayar'   => (clone $q)->where('status', 'pembayaran_diproses')->count(),
            'lunas'         => (clone $q)->where('status', 'pembayaran_lunas')->count(),
            'selesai'       => (clone $q)->where('status', 'selesai')->count(),
        ];
    }

    public function getRecords(): \Illuminate\Support\Collection
    {
        return $this->filteredQuery()->get();
    }

    public function getSekolahOptions(): array
    {
        if (!auth()->user()->hasAnyRole(['superadmin', 'admin_yayasan'])) return [];
        return Sekolah::orderBy('nama_sekolah')->pluck('nama_sekolah', 'id')->toArray();
    }

    public function getJurusanOptions(): array
    {
        $q = Jurusan::orderBy('nama_jurusan');
        if (!auth()->user()->hasAnyRole(['superadmin', 'admin_yayasan'])) {
            $sid = auth()->user()->adminSekolah?->sekolah_id;
            if ($sid) $q->where('sekolah_id', $sid);
        }
        return $q->pluck('nama_jurusan', 'id')->toArray();
    }

    public function getStatusOptions(): array
    {
        return [
            'diproses'            => 'Diproses',
            'diverifikasi'        => 'Diverifikasi',
            'diterima'            => 'Diterima',
            'ditolak'             => 'Ditolak',
            'menunggu_pembayaran' => 'Menunggu Bayar',
            'pembayaran_diproses' => 'Bayar Diproses',
            'pembayaran_lunas'    => 'Bayar Lunas',
            'selesai'             => 'Selesai',
        ];
    }

    public function getStatusLabel(string $status): string
    {
        return $this->getStatusOptions()[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    public function resetFilters(): void
    {
        $this->filterDari = $this->filterSampai = $this->filterSekolahId
            = $this->filterJurusanId = $this->filterStatus = null;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label('Export Excel')
                ->icon(Heroicon::TableCells)
                ->color(Color::Green)
                ->url(fn() => route('admin.export.laporan.excel', array_filter([
                    'date_from'  => $this->filterDari,
                    'date_to'    => $this->filterSampai,
                    'sekolah_id' => $this->filterSekolahId,
                    'jurusan_id' => $this->filterJurusanId,
                    'status'     => $this->filterStatus,
                ])))
                ->openUrlInNewTab(),
            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon(Heroicon::DocumentText)
                ->color(Color::Red)
                ->url(fn() => route('admin.export.laporan.pdf', array_filter([
                    'date_from'  => $this->filterDari,
                    'date_to'    => $this->filterSampai,
                    'sekolah_id' => $this->filterSekolahId,
                    'jurusan_id' => $this->filterJurusanId,
                    'status'     => $this->filterStatus,
                ])))
                ->openUrlInNewTab(),
        ];
    }
}
