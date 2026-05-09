<x-filament-panels::page>
<style>
.ppdb-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.06)}
.ppdb-card-header{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #e5e7eb;margin:-20px -20px 16px}
.ppdb-card-title{display:flex;align-items:center;gap:10px;font-size:15px;font-weight:600;color:#111827}
.ppdb-grid-filter{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px}
.ppdb-label{display:block;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px}
.ppdb-input{width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;color:#111827;background:#fff;box-sizing:border-box}
.ppdb-input:focus{outline:2px solid #16a34a;border-color:#16a34a}
.ppdb-btn-reset{width:100%;display:flex;align-items:center;justify-content:center;gap:6px;padding:8px 12px;font-size:13px;font-weight:500;color:#374151;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;cursor:pointer}
.ppdb-btn-reset:hover{background:#e5e7eb}
.ppdb-badges{display:flex;flex-wrap:wrap;gap:6px;margin-top:12px;padding-top:12px;border-top:1px solid #f3f4f6}
.ppdb-badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600}
.ppdb-summary-table{width:100%;border-collapse:collapse;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.08);margin-bottom:20px}
.ppdb-summary-table thead tr{background:#16a34a}
.ppdb-summary-table thead th{padding:14px 10px;color:#fff;font-size:13px;font-weight:700;text-align:center;letter-spacing:.3px;border-right:1px solid rgba(255,255,255,.2)}
.ppdb-summary-table thead th:last-child{border-right:none}
.ppdb-summary-table tbody tr{background:#f0fdf4}
.ppdb-summary-table tbody td{padding:16px 10px;text-align:center;font-size:20px;font-weight:700;border-right:1px solid #bbf7d0;color:#111827}
.ppdb-summary-table tbody td:last-child{border-right:none}
.ppdb-summary-table tbody td.c-green{color:#16a34a}
.ppdb-summary-table tbody td.c-red{color:#dc2626}
.ppdb-summary-table tbody td.c-blue{color:#2563eb}
.ppdb-summary-table tbody td.c-amber{color:#d97706}
.ppdb-summary-table tbody td.c-teal{color:#0d9488;font-size:14px}
.ppdb-table{width:100%;border-collapse:collapse;font-size:13px}
.ppdb-table thead tr{background:#f9fafb;border-bottom:2px solid #e5e7eb}
.ppdb-table thead th{padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
.ppdb-table tbody tr{border-bottom:1px solid #f3f4f6;transition:background .1s}
.ppdb-table tbody tr:hover{background:#f9fafb}
.ppdb-table tbody tr:nth-child(even){background:#fafafa}
.ppdb-table tbody td{padding:10px 14px;color:#374151;vertical-align:middle}
.ppdb-code{font-family:monospace;font-size:11px;font-weight:700;background:#f3f4f6;padding:2px 7px;border-radius:5px;color:#374151}
.ppdb-pill{display:inline-flex;align-items:center;padding:2px 9px;border-radius:999px;font-size:11px;font-weight:600;white-space:nowrap}
.ppdb-empty{text-align:center;padding:60px 20px;color:#9ca3af}
.ppdb-footer{padding:10px 20px;background:#f9fafb;border-top:1px solid #f3f4f6;border-radius:0 0 12px 12px;font-size:12px;color:#9ca3af}
</style>

{{-- ══ FILTER ══════════════════════════════════════════════════════════════ --}}
<div class="ppdb-card" style="margin-bottom:20px">
    <div class="ppdb-card-header">
        <div class="ppdb-card-title">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#16a34a"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/></svg>
            Filter Laporan Pembayaran
        </div>
    </div>
    <div class="ppdb-grid-filter">
        <div>
            <label class="ppdb-label">Dari Tanggal Bayar</label>
            <input type="date" wire:model.live="filterDari" class="ppdb-input"/>
        </div>
        <div>
            <label class="ppdb-label">Sampai Tanggal</label>
            <input type="date" wire:model.live="filterSampai" class="ppdb-input"/>
        </div>
        @if(auth()->user()->hasAnyRole(['superadmin','admin_yayasan']))
        <div>
            <label class="ppdb-label">Sekolah</label>
            <select wire:model.live="filterSekolahId" class="ppdb-input">
                <option value="">Semua Sekolah</option>
                @foreach($this->getSekolahOptions() as $id => $nama)
                    <option value="{{ $id }}" @selected($filterSekolahId == $id)>{{ $nama }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div>
            <label class="ppdb-label">Jurusan</label>
            <select wire:model.live="filterJurusanId" class="ppdb-input">
                <option value="">Semua Jurusan</option>
                @foreach($this->getJurusanOptions() as $id => $nama)
                    <option value="{{ $id }}" @selected($filterJurusanId == $id)>{{ $nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="ppdb-label">Status Pembayaran</label>
            <select wire:model.live="filterStatus" class="ppdb-input">
                <option value="">Semua Status</option>
                @foreach($this->getStatusOptions() as $val => $label)
                    <option value="{{ $val }}" @selected($filterStatus == $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;align-items:flex-end">
            <button wire:click="resetFilters" class="ppdb-btn-reset">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                Reset
            </button>
        </div>
    </div>
    @if($filterDari || $filterSampai || $filterSekolahId || $filterJurusanId || $filterStatus)
    <div class="ppdb-badges">
        @if($filterDari)<span class="ppdb-badge" style="background:#dcfce7;color:#15803d">Dari: {{ \Carbon\Carbon::parse($filterDari)->format('d/m/Y') }}</span>@endif
        @if($filterSampai)<span class="ppdb-badge" style="background:#dcfce7;color:#15803d">S/D: {{ \Carbon\Carbon::parse($filterSampai)->format('d/m/Y') }}</span>@endif
        @if($filterSekolahId)<span class="ppdb-badge" style="background:#dbeafe;color:#1d4ed8">{{ \App\Models\Sekolah::find($filterSekolahId)?->nama_sekolah }}</span>@endif
        @if($filterJurusanId)<span class="ppdb-badge" style="background:#ede9fe;color:#6d28d9">{{ \App\Models\Jurusan::find($filterJurusanId)?->nama_jurusan }}</span>@endif
        @if($filterStatus)<span class="ppdb-badge" style="background:#fef3c7;color:#92400e">{{ $this->getStatusLabel($filterStatus) }}</span>@endif
    </div>
    @endif
</div>

{{-- ══ SUMMARY CARDS ══════════════════════════════════════════════════════ --}}
@php $s = $this->getSummary(); @endphp
<table class="ppdb-summary-table">
    <thead>
        <tr>
            <th>Total Transaksi</th>
            <th>Lunas</th>
            <th>Menunggu Verif.</th>
            <th>Pending</th>
            <th>Gagal/Kadaluarsa</th>
            <th>Total Nominal</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $s['total'] }}</td>
            <td class="c-green">{{ $s['lunas'] }}</td>
            <td class="c-blue">{{ $s['menunggu'] }}</td>
            <td class="c-amber">{{ $s['pending'] }}</td>
            <td class="c-red">{{ $s['gagal'] }}</td>
            <td class="c-teal">Rp {{ number_format($s['totalNominal'],0,',','.') }}<br><small style="font-size:12px;color:#6b7280">Lunas: Rp {{ number_format($s['totalLunas'],0,',','.') }}</small></td>
        </tr>
    </tbody>
</table>

{{-- ══ TABEL ══════════════════════════════════════════════════════════════ --}}
@php $records = $this->getRecords(); @endphp
<div class="ppdb-card">
    <div class="ppdb-card-header">
        <div class="ppdb-card-title">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#16a34a"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
            Rekap Pembayaran
            <span class="ppdb-badge" style="background:#dcfce7;color:#15803d;font-size:12px">{{ $records->count() }} transaksi</span>
        </div>
        <span style="font-size:12px;color:#9ca3af">Dicetak: {{ now()->format('d/m/Y H:i') }}</span>
    </div>
    <div style="overflow-x:auto">
        <table class="ppdb-table">
            <thead>
                <tr>
                    <th>#</th><th>Kode Reg.</th><th>Nama Siswa</th><th>Sekolah</th>
                    <th>Jurusan</th><th>Metode</th><th>Nominal</th>
                    <th>Status</th><th>Tgl Bayar</th><th>Verifikator</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $i => $r)
                @php
                    $bc = match($r->status_pembayaran ?? '') {
                        'sukses'              => ['#d1fae5','#065f46'],
                        'menunggu_verifikasi' => ['#dbeafe','#1e40af'],
                        'pending'             => ['#fef3c7','#92400e'],
                        'gagal','kadaluarsa'  => ['#fee2e2','#991b1b'],
                        default               => ['#f3f4f6','#6b7280'],
                    };
                @endphp
                <tr>
                    <td style="color:#9ca3af;font-size:12px">{{ $i+1 }}</td>
                    <td><span class="ppdb-code">{{ $r->pendaftaran?->kode_regis ?? '-' }}</span></td>
                    <td style="font-weight:600;color:#111827">{{ $r->pendaftaran?->siswa?->nama_siswa ?? '-' }}</td>
                    <td style="font-size:12px;max-width:130px">{{ $r->pendaftaran?->sekolah?->nama_sekolah ?? '-' }}</td>
                    <td style="font-size:12px;max-width:120px">{{ $r->pendaftaran?->jurusan?->nama_jurusan ?? '-' }}</td>
                    <td><span class="ppdb-pill" style="background:#f3f4f6;color:#374151">{{ ucfirst($r->metodePembayaran?->nama_metode ?? '-') }}</span></td>
                    <td style="font-weight:600;color:#111827;white-space:nowrap">Rp {{ number_format($r->nominal ?? 0,0,',','.') }}</td>
                    <td><span class="ppdb-pill" style="background:{{ $bc[0] }};color:{{ $bc[1] }}">{{ $this->getStatusLabel($r->status_pembayaran ?? '') }}</span></td>
                    <td style="font-size:12px;white-space:nowrap;color:#6b7280">{{ $r->tanggal_pembayaran?->format('d/m/Y') ?? '-' }}</td>
                    <td style="font-size:12px;color:#6b7280">{{ $r->verifikator?->name ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="10" class="ppdb-empty">
                    <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="#d1d5db" style="margin:0 auto 12px;display:block"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75"/></svg>
                    <div style="font-weight:600;font-size:14px;margin-bottom:4px">Tidak ada data pembayaran</div>
                    <div style="font-size:12px">Coba ubah filter pencarian</div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($records->count() > 0)
    <div class="ppdb-footer">Menampilkan <strong>{{ $records->count() }}</strong> transaksi pembayaran</div>
    @endif
</div>
</x-filament-panels::page>
