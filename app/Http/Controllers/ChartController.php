<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Pendaftaran;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChartController extends Controller
{
    private static array $palette = [
        '#16a34a','#2563eb','#d97706','#7c3aed',
        '#db2777','#0891b2','#ea580c','#65a30d',
    ];

    public function iframe(Request $request)
    {
        if (!Auth::check()) abort(403);
        $pendaftaranUrl = route('filament.admin.resources.pendaftaran.index');
        return response()
            ->view('chart.iframe', compact('pendaftaranUrl'))
            ->header('X-Frame-Options', 'SAMEORIGIN');
    }

    public function data(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user     = Auth::user();
        $isGlobal = $user->hasAnyRole(['superadmin', 'admin_yayasan']);

        return response()->json(
            $isGlobal
                ? $this->buildDrilldownData()
                : $this->buildPerJurusanData($user)
        );
    }

    private function buildDrilldownData(): array
    {
        $sekolahs      = Sekolah::with('jurusans')->orderBy('nama_sekolah')->get();
        $pendaftaranUrl = route('filament.admin.resources.pendaftaran.index');
        $seriesData    = [];
        $drilldowns    = [];
        $legend        = [];

        foreach ($sekolahs as $idx => $s) {
            $color = self::$palette[$idx % count(self::$palette)];
            $total = Pendaftaran::where('sekolah_id', $s->id)->count();

            $seriesData[] = [
                'name'       => $s->nama_sekolah,
                'y'          => $total,
                'color'      => $color,
                'drilldown'  => 'sekolah-' . $s->id,
                'sekolah_id' => $s->id,
            ];

            $legend[] = ['name' => $s->nama_sekolah, 'color' => $color];

            // ── Bangun data drilldown per jurusan ──────────────────────────
            $jData = [];

            // Kasus 1: sekolah PUNYA jurusan → group by jurusan
            if ($s->jurusans->isNotEmpty()) {
                foreach ($s->jurusans->sortBy('nama_jurusan') as $j) {
                    $count = Pendaftaran::where('sekolah_id', $s->id)
                                ->where('jurusan_id', $j->id)
                                ->count();
                    // Tetap tampilkan meski 0 agar transparan
                    $jData[] = [
                        'name'        => $j->nama_jurusan,
                        'y'           => $count,
                        'color'       => $color,
                        'sekolah_id'  => $s->id,
                        'jurusan_id'  => $j->id,
                        'direct_url'  => $pendaftaranUrl
                                         . '?tableFilters[sekolah_id][value]=' . $s->id
                                         . '&tableFilters[jurusan_id][value]='  . $j->id,
                    ];
                }

                // Tambah baris "Tanpa Jurusan" jika ada pendaftar null jurusan_id
                $nullCount = Pendaftaran::where('sekolah_id', $s->id)
                                ->whereNull('jurusan_id')
                                ->count();
                if ($nullCount > 0) {
                    $jData[] = [
                        'name'       => '— Tanpa Jurusan',
                        'y'          => $nullCount,
                        'color'      => '#9ca3af',
                        'sekolah_id' => $s->id,
                        'jurusan_id' => null,
                        'direct_url' => $pendaftaranUrl
                                        . '?tableFilters[sekolah_id][value]=' . $s->id,
                    ];
                }
            } else {
                // Kasus 2: sekolah TIDAK punya jurusan (misal SMP) →
                // tampilkan 1 bar "Semua Pendaftar" yang langsung link ke pendaftaran
                $jData[] = [
                    'name'       => 'Semua Pendaftar (' . $s->nama_sekolah . ')',
                    'y'          => $total,
                    'color'      => $color,
                    'sekolah_id' => $s->id,
                    'jurusan_id' => null,
                    'direct_url' => $pendaftaranUrl
                                    . '?tableFilters[sekolah_id][value]=' . $s->id,
                ];
            }

            $drilldowns[] = [
                'id'   => 'sekolah-' . $s->id,
                'name' => $s->nama_sekolah,
                'data' => $jData,
            ];
        }

        // Ubah ke multi-series agar legend bisa toggle per sekolah
        // Setiap sekolah jadi 1 series dengan 1 data point
        $multiSeries = [];
        foreach ($seriesData as $point) {
            $multiSeries[] = [
                'name'        => $point['name'],
                'color'       => $point['color'],
                'showInLegend'=> true,
                'data'        => [$point],  // 1 point per series
            ];
        }

        return [
            'mode'           => 'drilldown',
            'series'         => $multiSeries,
            'drilldowns'     => $drilldowns,
            'legend'         => $legend,
            'pendaftaranUrl' => $pendaftaranUrl,
        ];
    }

    private function buildPerJurusanData($user): array
    {
        $sid           = $user->adminSekolah?->sekolah_id;
        $pendaftaranUrl = route('filament.admin.resources.pendaftaran.index');

        if (!$sid) return [
            'mode' => 'empty', 'series' => [],
            'drilldowns' => [], 'legend' => [],
            'pendaftaranUrl' => $pendaftaranUrl,
        ];

        $sekolah   = Sekolah::with('jurusans')->find($sid);
        $jurusans  = $sekolah?->jurusans->sortBy('nama_jurusan') ?? collect();
        $seriesData = [];

        if ($jurusans->isNotEmpty()) {
            foreach ($jurusans as $idx => $j) {
                $count = Pendaftaran::where('sekolah_id', $sid)
                            ->where('jurusan_id', $j->id)->count();
                $seriesData[] = [
                    'name'       => $j->nama_jurusan,
                    'y'          => $count,
                    'color'      => self::$palette[$idx % count(self::$palette)],
                    'sekolah_id' => $sid,
                    'jurusan_id' => $j->id,
                    'direct_url' => $pendaftaranUrl
                                    . '?tableFilters[sekolah_id][value]=' . $sid
                                    . '&tableFilters[jurusan_id][value]='  . $j->id,
                ];
            }
        } else {
            // Sekolah tanpa jurusan → 1 bar saja
            $total = Pendaftaran::where('sekolah_id', $sid)->count();
            $seriesData[] = [
                'name'       => 'Semua Pendaftar',
                'y'          => $total,
                'color'      => self::$palette[0],
                'sekolah_id' => $sid,
                'jurusan_id' => null,
                'direct_url' => $pendaftaranUrl . '?tableFilters[sekolah_id][value]=' . $sid,
            ];
        }

        return [
            'mode'           => 'per_jurusan',
            'series'         => $seriesData,
            'drilldowns'     => [],
            'legend'         => [],
            'pendaftaranUrl' => $pendaftaranUrl,
        ];
    }
}
