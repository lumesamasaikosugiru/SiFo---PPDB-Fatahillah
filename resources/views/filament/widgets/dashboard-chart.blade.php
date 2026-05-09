<x-filament-widgets::widget>
<style>
.ppdb-chart-wrap{background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.07)}
.ppdb-chart-head{padding:14px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:10px}
.ppdb-chart-title{font-size:15px;font-weight:700;color:#111827}
.ppdb-chart-sub{font-size:12px;color:#6b7280;margin-top:2px}
</style>

<div class="ppdb-chart-wrap">
    <div class="ppdb-chart-head">
        <span style="font-size:20px">📈</span>
        <div>
            <div class="ppdb-chart-title">Statistik Pendaftar</div>
            <div class="ppdb-chart-sub">Drilldown: Sekolah → Jurusan → klik untuk lihat daftar pendaftar</div>
        </div>
    </div>
    {{-- Iframe dengan id unik, height awal 500 supaya tidak collapse --}}
    <iframe
        id="ppdb-hc-frame"
        src="{{ route('chart.iframe') }}"
        width="100%"
        height="500"
        frameborder="0"
        scrolling="no"
        style="display:block;width:100%;border:none"
    ></iframe>
</div>
</x-filament-widgets::widget>
