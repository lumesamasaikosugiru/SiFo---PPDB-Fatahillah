<?php

namespace App\Filament\Widgets;

use App\Models\Jurusan;
use App\Models\Pendaftaran;
use App\Models\Sekolah;
use Filament\Widgets\Widget;

class DashboardWidget extends Widget
{
    protected string $view      = 'filament.widgets.dashboard-chart';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 2;

    private static array $palette = [
        '#16a34a','#2563eb','#d97706','#7c3aed',
        '#db2777','#0891b2','#ea580c','#65a30d',
    ];

    protected function getViewData(): array
    {
        $user     = auth()->user();
        $isGlobal = $user->hasAnyRole(['superadmin', 'admin_yayasan']);
        return ['chartData' => $isGlobal
            ? $this->drilldownData()
            : $this->perJurusanData($user)
        ];
    }

    private function drilldownData(): array
    {
        $sekolahs   = Sekolah::with('jurusans')->orderBy('nama_sekolah')->get();
        $labels     = [];
        $counts     = [];
        $colors     = [];
        $drilldown  = [];
        $legend     = [];

        foreach ($sekolahs as $idx => $s) {
            $color   = self::$palette[$idx % count(self::$palette)];
            // Hitung SEMUA pendaftar — konsisten dengan StatsOverview
            $total   = Pendaftaran::where('sekolah_id', $s->id)->count();
            $labels[]  = $s->nama_sekolah;
            $counts[]  = $total;
            $colors[]  = $color;
            $legend[]  = ['name' => $s->nama_sekolah, 'color' => $color];

            $jData = [];
            foreach ($s->jurusans->sortBy('nama_jurusan') as $j) {
                $c = Pendaftaran::where('jurusan_id', $j->id)->count();
                if ($c === 0) continue;
                $jData[] = [
                    'label'      => $j->nama_jurusan,
                    'count'      => $c,
                    'color'      => $color,
                    'sekolah_id' => $s->id,
                    'jurusan_id' => $j->id,
                ];
            }
            $drilldown[$idx] = $jData;
        }

        return [
            'mode'           => 'drilldown',
            'labels'         => $labels,
            'counts'         => $counts,
            'colors'         => $colors,
            'drilldown'      => $drilldown,
            'legend'         => $legend,
            'pendaftaranUrl' => route('filament.admin.resources.pendaftaran.index'),
        ];
    }

    private function perJurusanData($user): array
    {
        $sid = $user->adminSekolah?->sekolah_id;
        if (!$sid) return ['mode'=>'empty','labels'=>[],'counts'=>[],'colors'=>[],'drilldown'=>[],'legend'=>[],'pendaftaranUrl'=>''];

        $jurusans  = Jurusan::where('sekolah_id', $sid)->orderBy('nama_jurusan')->get();
        $labels    = [];
        $counts    = [];
        $colors    = [];
        $drillItems= [];

        foreach ($jurusans as $idx => $j) {
            $c = Pendaftaran::where('jurusan_id', $j->id)->count();
            if ($c === 0) continue;
            $labels[]   = $j->nama_jurusan;
            $counts[]   = $c;
            $colors[]   = self::$palette[$idx % count(self::$palette)];
            $drillItems[] = ['sekolah_id' => $sid, 'jurusan_id' => $j->id];
        }

        return [
            'mode'           => 'per_jurusan',
            'labels'         => $labels,
            'counts'         => $counts,
            'colors'         => $colors,
            'drilldown'      => [],
            'drillItems'     => $drillItems,
            'legend'         => [],
            'pendaftaranUrl' => route('filament.admin.resources.pendaftaran.index'),
        ];
    }
}
