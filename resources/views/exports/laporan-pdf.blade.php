<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 9px; color: #1a1a1a; }
.header { background: #16a34a; color: #fff; padding: 12px 18px 10px; }
.header h1 { font-size: 14px; font-weight: bold; letter-spacing: .3px; }
.header p  { font-size: 8px; margin-top: 3px; opacity: .85; }
.meta { padding: 8px 18px 4px; }
.meta-item { font-size: 8.5px; color: #555; display: inline-block; margin-right: 20px; margin-bottom: 3px; }
.meta-item span { font-weight: bold; color: #1a1a1a; }
.divider { border: none; border-top: 1px solid #e5e7eb; margin: 4px 18px 6px; }

/* Summary boxes */
.summary-table { margin: 6px 18px 8px; width: calc(100% - 36px); border-collapse: collapse; font-size: 9px; border-radius:4px; overflow:hidden; }
.summary-table thead tr { background: #16a34a; }
.summary-table thead th { padding: 8px 10px; color: #fff; font-size: 9px; font-weight: bold; text-align: center; border-right: 1px solid rgba(255,255,255,.3); }
.summary-table thead th:last-child { border-right: none; }
.summary-table tbody tr { background: #f0fdf4; }
.sum-cell { padding: 8px 10px; border-right: 1px solid #bbf7d0; vertical-align: middle; text-align: center; }
.sum-cell:last-child { border-right: none; }
.sum-val { font-size: 18px; font-weight: bold; color: #111827; }
.sum-val.green { color: #16a34a; }
.sum-val.red { color: #dc2626; }
.sum-val.blue { color: #2563eb; }
.sum-val.amber { color: #d97706; }
.sum-val.teal { color: #0d9488; }

/* Main table */
table.main { width: calc(100% - 36px); margin: 0 18px; border-collapse: collapse; font-size: 8.5px; }
table.main thead tr { background: #16a34a; color: #fff; }
table.main thead th { padding: 5px 5px; text-align: left; font-weight: bold; white-space: nowrap; }
table.main tbody tr:nth-child(even) { background: #f0fdf4; }
table.main tbody tr { border-bottom: .5px solid #e5e7eb; }
table.main tbody td { padding: 4px 5px; vertical-align: middle; }

.badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 7.5px; font-weight: bold; white-space: nowrap; }
.s-diproses { background:#fef9c3; color:#854d0e; }
.s-diverifikasi { background:#dbeafe; color:#1e3a8a; }
.s-diterima { background:#dcfce7; color:#14532d; }
.s-ditolak { background:#fee2e2; color:#7f1d1d; }
.s-menunggu_pembayaran { background:#ffedd5; color:#7c2d12; }
.s-pembayaran_diproses { background:#e0e7ff; color:#312e81; }
.s-pembayaran_lunas { background:#d1fae5; color:#064e3b; }
.s-selesai { background:#f3f4f6; color:#374151; }
.s-bayar-lunas { background:#d1fae5; color:#064e3b; }
.s-bayar-belum { background:#f3f4f6; color:#6b7280; }
.s-bayar-pending { background:#fef9c3; color:#854d0e; }

.footer { margin-top: 12px; padding: 6px 18px; font-size: 7.5px; color: #9ca3af; border-top: 1px solid #e5e7eb; display: table; width: 100%; }
.footer span:last-child { float: right; }
</style>
</head>
<body>

<div class="header">
  <h1>PPDB Terpusat YPFC &mdash; Laporan Rekap Pendaftar</h1>
  <p>Dicetak: {{ now()->format('d F Y, H:i') }} &nbsp;&bull;&nbsp; Total: {{ count($records) }} data</p>
</div>

<div class="meta">
  @if($dateFrom || $dateTo)
  <div class="meta-item">Periode: <span>{{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') : '&mdash;' }} s/d {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('d/m/Y') : '&mdash;' }}</span></div>
  @else
  <div class="meta-item">Periode: <span>Semua tanggal</span></div>
  @endif
  @if($status)<div class="meta-item">Status: <span>{{ ucfirst(str_replace('_',' ',$status)) }}</span></div>@endif
  @if($sekolahNama)<div class="meta-item">Sekolah: <span>{{ $sekolahNama }}</span></div>@endif
  @if($jurusanNama)<div class="meta-item">Jurusan: <span>{{ $jurusanNama }}</span></div>@endif
</div>

{{-- Summary boxes --}}
@php
  $rDisetujui   = collect($records)->where('status','diproses')->count();
  $rDiverif     = collect($records)->where('status','diverifikasi')->count();
  $rDiterima    = collect($records)->where('status','diterima')->count();
  $rDitolak     = collect($records)->where('status','ditolak')->count();
  $rBlmBayar    = collect($records)->where('status','menunggu_pembayaran')->count();
  $rProsesBayar = collect($records)->where('status','pembayaran_diproses')->count();
  $rLunas       = collect($records)->where('status','pembayaran_lunas')->count();
  $rSelesai     = collect($records)->where('status','selesai')->count();
@endphp
<table class="summary-table">
  <thead>
    <tr>
      <th>Total</th>
      <th>Diproses</th>
      <th>Diverifikasi</th>
      <th>Diterima</th>
      <th>Ditolak</th>
      <th>Belum Bayar</th>
      <th>Proses Bayar</th>
      <th>Sudah Bayar</th>
      <th>Selesai</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="sum-cell"><div class="sum-val">{{ count($records) }}</div></td>
      <td class="sum-cell"><div class="sum-val amber">{{ $rDisetujui }}</div></td>
      <td class="sum-cell"><div class="sum-val blue">{{ $rDiverif }}</div></td>
      <td class="sum-cell"><div class="sum-val green">{{ $rDiterima }}</div></td>
      <td class="sum-cell"><div class="sum-val red">{{ $rDitolak }}</div></td>
      <td class="sum-cell"><div class="sum-val amber">{{ $rBlmBayar }}</div></td>
      <td class="sum-cell"><div class="sum-val blue">{{ $rProsesBayar }}</div></td>
      <td class="sum-cell"><div class="sum-val green">{{ $rLunas }}</div></td>
      <td class="sum-cell"><div class="sum-val teal">{{ $rSelesai }}</div></td>
    </tr>
  </tbody>
</table>

<hr class="divider">

<table class="main">
  <thead>
    <tr>
      <th style="width:20px">#</th>
      <th style="width:80px">Kode Registrasi</th>
      <th>Nama Calon Siswa</th>
      <th style="width:65px">NISN</th>
      <th>Sekolah Tujuan</th>
      <th>Jurusan</th>
      <th style="width:55px">Jalur</th>
      <th style="width:50px">Tgl Submit</th>
      <th style="width:70px">Status</th>
      <th style="width:65px">Pembayaran</th>
    </tr>
  </thead>
  <tbody>
    @forelse($records as $i => $r)
    @php
      $latestBayar = $r->pembayarans->sortByDesc('created_at')->first();
      $bayarStatus = $latestBayar?->status_pembayaran;
      $bayarLabel  = match($bayarStatus) {
          'sukses'              => 'Lunas',
          'menunggu_verifikasi' => 'Menunggu Verif',
          'pending'             => 'Pending',
          'gagal'               => 'Gagal',
          'kadaluarsa'          => 'Kadaluarsa',
          default               => 'Belum Bayar',
      };
      $bayarClass = match($bayarStatus) {
          'sukses'              => 's-bayar-lunas',
          'menunggu_verifikasi' => 's-diverifikasi',
          'pending'             => 's-bayar-pending',
          default               => 's-bayar-belum',
      };
    @endphp
    <tr>
      <td style="color:#9ca3af;font-family:monospace">{{ $i+1 }}</td>
      <td style="font-family:monospace;font-size:8px;font-weight:bold">{{ $r->kode_regis ?? '-' }}</td>
      <td>
        <strong>{{ $r->siswa?->nama_siswa ?? '-' }}</strong>
        @if($r->siswa?->asal_sekolah)
        <br><span style="font-size:7.5px;color:#6b7280">{{ $r->siswa->asal_sekolah }}</span>
        @endif
      </td>
      <td style="font-family:monospace;font-size:7.5px">{{ $r->siswa?->nisn ?? '-' }}</td>
      <td style="font-size:8px">{{ $r->sekolah?->nama_sekolah ?? '-' }}</td>
      <td style="font-size:8px">{{ $r->jurusan?->nama_jurusan ?? '-' }}</td>
      <td><span class="badge s-diproses">{{ ucfirst($r->jalur_pendaftaran ?? '-') }}</span></td>
      <td style="font-size:7.5px">{{ $r->tanggal_submit?->format('d/m/Y') ?? '-' }}</td>
      <td><span class="badge s-{{ $r->status }}">{{ ucfirst(str_replace('_',' ',$r->status ?? '-')) }}</span></td>
      <td>
        <span class="badge {{ $bayarClass }}">{{ $bayarLabel }}</span>
        @if($latestBayar?->nominal)
        <br><span style="font-size:7px;color:#6b7280">Rp {{ number_format($latestBayar->nominal,0,',','.') }}</span>
        @endif
      </td>
    </tr>
    @empty
    <tr><td colspan="10" style="text-align:center;padding:20px;color:#9ca3af">Tidak ada data</td></tr>
    @endforelse
  </tbody>
</table>

<div class="footer">
  <span>PPDB Terpusat Yayasan Fatahillah Cilegon</span>
  <span>Dokumen ini dicetak otomatis oleh sistem &bull; {{ now()->format('d/m/Y H:i') }}</span>
</div>

</body>
</html>
