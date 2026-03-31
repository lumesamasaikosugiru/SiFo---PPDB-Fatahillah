<?php

namespace App\Filament\Widgets;

use App\Models\Jurusan;
use Filament\Widgets\ChartWidget;

class DashboardWidget extends ChartWidget
{
    protected ?string $heading = 'Statistik Pendaftar Perjurusan';
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $jurusan = Jurusan::query()
            ->withCount('pendaftarans')
            ->orderBy('nama_jurusan')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pendaftar',
                    'data' => $jurusan->pluck('pendaftarans_count'),
                    'backgroundColor' => [
                        '#22c55e', // hijauu
                        '#f59e0b', // kuninng
                        '#ef4444', // meraah
                        '#8b5cf6', // unguu
                        '#06b6d4', // cyann
                    ],
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $jurusan->pluck('nama_jurusan')
                ->map(fn($nama) => str()->limit($nama, 10)),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
