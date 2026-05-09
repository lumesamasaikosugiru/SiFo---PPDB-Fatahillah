<?php

namespace App\Filament\Pages;

use App\Models\Jurusan;
use App\Models\Pembayaran;
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

class LaporanPembayaran extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.laporan-pembayaran';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
    protected static ?string $navigationLabel               = 'Laporan Pembayaran';
    protected static ?string $title                         = 'Laporan Rekap Pembayaran';
    protected static ?int    $navigationSort                = 10;
    protected static string|UnitEnum|null $navigationGroup  = 'Report';
    protected static ?string $slug                          = 'laporan-pembayaran';

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
        $q    = Pembayaran::with(['pendaftaran.siswa', 'pendaftaran.sekolah', 'pendaftaran.jurusan', 'metodePembayaran', 'verifikator']);
        if (!$user->hasAnyRole(['superadmin', 'admin_yayasan'])) {
            $sid = $user->adminSekolah?->sekolah_id;
            $q   = $sid
                ? $q->whereHas('pendaftaran', fn($pq) => $pq->where('sekolah_id', $sid))
                : $q->whereRaw('0=1');
        }
        return $q;
    }

    private function filteredQuery(): Builder
    {
        $q = $this->baseQuery();
        if ($this->filterDari)      $q->whereDate('tanggal_pembayaran', '>=', $this->filterDari);
        if ($this->filterSampai)    $q->whereDate('tanggal_pembayaran', '<=', $this->filterSampai);
        if ($this->filterSekolahId) $q->whereHas('pendaftaran', fn($pq) => $pq->where('sekolah_id', $this->filterSekolahId));
        if ($this->filterJurusanId) $q->whereHas('pendaftaran', fn($pq) => $pq->where('jurusan_id', $this->filterJurusanId));
        if ($this->filterStatus)    $q->where('status_pembayaran', $this->filterStatus);
        return $q->orderBy('tanggal_pembayaran', 'desc');
    }

    public function getSummary(): array
    {
        $all = $this->filteredQuery()->get();
        return [
            'total'        => $all->count(),
            'lunas'        => $all->where('status_pembayaran', 'sukses')->count(),
            'menunggu'     => $all->where('status_pembayaran', 'menunggu_verifikasi')->count(),
            'pending'      => $all->where('status_pembayaran', 'pending')->count(),
            'gagal'        => $all->whereIn('status_pembayaran', ['gagal', 'kadaluarsa'])->count(),
            'totalNominal' => $all->sum('nominal'),
            'totalLunas'   => $all->where('status_pembayaran', 'sukses')->sum('nominal'),
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
            'pending'             => 'Menunggu Bayar',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'sukses'              => 'Lunas',
            'gagal'               => 'Gagal',
            'kadaluarsa'          => 'Kadaluarsa',
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
                ->url(fn() => route('admin.export.pembayaran.excel', array_filter([
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
                ->url(fn() => route('admin.export.pembayaran.pdf', array_filter([
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
