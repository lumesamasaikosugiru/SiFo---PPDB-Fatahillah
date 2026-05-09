<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 9px; color: #1a1a1a; }
.header { background: #16a34a; color: #fff; padding: 12px 18px 10px; }
.header h1 { font-size: 14px; font-weight: bold; }
.header p  { font-size: 8px; margin-top: 3px; opacity: .85; }
.meta { padding: 8px 18px 4px; display: flex; flex-wrap: wrap; gap: 20px; }
.meta-item { font-size: 8.5px; color: #555; }
.meta-item span { font-weight: bold; color: #1a1a1a; }
.sum-val { font-size: 13px; font-weight: bold; color: #15803d; }
.sum-lbl { font-size: 7.5px; color: #666; margin-top: 2px; }
.divider { border: none; border-top: 1px solid #e5e7eb; margin: 6px 18px; }
table { width: calc(100% - 36px); margin: 0 18px; border-collapse: collapse; font-size: 8.5px; }
thead tr { background: #16a34a; color: #fff; }
thead th { padding: 5px 6px; text-align: left; font-weight: bold; white-space: nowrap; }
tbody tr:nth-child(even) { background: #f0fdf4; }
tbody tr { border-bottom: .5px solid #e5e7eb; }
tbody td { padding: 4px 6px; vertical-align: middle; }
.badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 7.5px; font-weight: bold; }
.sp { background:#fef9c3; color:#854d0e; }
.sv { background:#dbeafe; color:#1e3a8a; }
.ss { background:#dcfce7; color:#14532d; }
.sg { background:#fee2e2; color:#7f1d1d; }
.sk { background:#f3f4f6; color:#374151; }
.footer { margin-top: 12px; padding: 6px 18px; font-size: 7.5px; color: #9ca3af; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; }
.summary-table { margin: 6px 18px 8px; width: calc(100% - 36px); border-collapse: collapse; font-size: 9px; }
.summary-table thead tr { background: #16a34a; }
.summary-table thead th { padding: 7px 8px; color: #fff; font-size: 8.5px; font-weight: bold; text-align: center; border-right: 1px solid rgba(255,255,255,.3); }
.summary-table thead th:last-child { border-right: none; }
.summary-table tbody tr { background: #f0fdf4; }
.sum-cell { padding: 7px 8px; border-right: 1px solid #bbf7d0; vertical-align: middle; text-align: center; }
.sum-cell:last-child { border-right: none; }
.sum-val { font-size: 14px; font-weight: bold; color: #111827; }
.sum-val.green { color: #16a34a; }
.sum-val.red { color: #dc2626; }
.sum-val.blue { color: #2563eb; }
.sum-val.amber { color: #d97706; }
.sum-val.teal { color: #0d9488; }
</style>
</head>
<body>
<div class="header">
  <h1>PPDB Terpusat YPFC &mdash; Laporan Data Pembayaran</h1>
  <p>Dicetak: {{ now()->format('d F Y, H:i') }} &nbsp;&bull;&nbsp; Total: {{ count($records) }} transaksi</p>
</div>

<div class="meta">
  @if($dateFrom || $dateTo)
  <div class="meta-item">Periode: <span>{{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') : '&mdash;' }} s/d {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('d/m/Y') : '&mdash;' }}</span></div>
  @else
  <div class="meta-item">Periode: <span>Semua tanggal</span></div>
  @endif
  @if($status)<div class="meta-item">Status: <span>{{ ucfirst(str_replace('_',' ',$status)) }}</span></div>@endif
  @if($sekolahNama)<div class="meta-item">Sekolah: <span>{{ $sekolahNama }}</span></div>@endif
  @if(isset($jurusanNama) && $jurusanNama)<div class="meta-item">Jurusan: <span>{{ $jurusanNama }}</span></div>@endif
</div>

@php
  $bTotal   = $records->count();
  $bLunas   = $records->where('status_pembayaran','sukses')->count();
  $bMenunggu= $records->where('status_pembayaran','menunggu_verifikasi')->count();
  $bPending = $records->where('status_pembayaran','pending')->count();
  $bGagal   = $records->whereIn('status_pembayaran',['gagal','kadaluarsa'])->count();
  $bNominal = $records->sum('nominal');
  $bNomLunas= $records->where('status_pembayaran','sukses')->sum('nominal');
@endphp
<table class="summary-table">
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
      <td class="sum-cell"><div class="sum-val">{{ $bTotal }}</div></td>
      <td class="sum-cell"><div class="sum-val green">{{ $bLunas }}</div></td>
      <td class="sum-cell"><div class="sum-val blue">{{ $bMenunggu }}</div></td>
      <td class="sum-cell"><div class="sum-val amber">{{ $bPending }}</div></td>
      <td class="sum-cell"><div class="sum-val red">{{ $bGagal }}</div></td>
      <td class="sum-cell">
        <div class="sum-val teal" style="font-size:11px">Rp {{ number_format($bNominal,0,',','.') }}</div>
        <div style="font-size:7px;color:#059669;margin-top:2px">Lunas: Rp {{ number_format($bNomLunas,0,',','.') }}</div>
      </td>
    </tr>
  </tbody>
</table>
<hr class="divider">

<table>
  <thead>
    <tr>
      <th width="20">No</th>
      <th>Kode Regis</th>
      <th>Nama Siswa</th>
      <th>Sekolah</th>
      <th>Jurusan</th>
      <th>Metode</th>
      <th>Nominal</th>
      <th>Status</th>
      <th>Tgl Bayar</th>
      <th>Verifikator</th>
      <th>Catatan</th>
    </tr>
  </thead>
  <tbody>
    @forelse($records as $i => $row)
    @php
      $bc = match($row->status_pembayaran) { 'pending'=>'sp','menunggu_verifikasi'=>'sv','sukses'=>'ss','gagal'=>'sg','kadaluarsa'=>'sk',default=>'' };
      $sl = match($row->status_pembayaran) { 'pending'=>'Menunggu','menunggu_verifikasi'=>'Verifikasi','sukses'=>'Lunas','gagal'=>'Gagal','kadaluarsa'=>'Kadaluarsa',default=>ucfirst($row->status_pembayaran) };
    @endphp
    <tr>
      <td style="text-align:center">{{ $i+1 }}</td>
      <td>{{ $row->pendaftaran?->kode_regis ?? '-' }}</td>
      <td>{{ $row->pendaftaran?->siswa?->nama_siswa ?? '-' }}</td>
      <td>{{ $row->pendaftaran?->sekolah?->nama_sekolah ?? '-' }}</td>
      <td>{{ $row->pendaftaran?->jurusan?->nama_jurusan ?? '-' }}</td>
      <td>{{ ucfirst($row->metodePembayaran?->nama_metode ?? '-') }}</td>
      <td style="text-align:right">Rp {{ number_format($row->nominal,0,',','.') }}</td>
      <td><span class="badge {{ $bc }}">{{ $sl }}</span></td>
      <td>{{ $row->tanggal_pembayaran?->format('d/m/Y') ?? '-' }}</td>
      <td>{{ $row->verifikator?->name ?? '-' }}</td>
      <td>{{ $row->catatan ?? '-' }}</td>
    </tr>
    @empty
    <tr><td colspan="11" style="text-align:center;padding:12px;color:#9ca3af;">Tidak ada data</td></tr>
    @endforelse
  </tbody>
</table>

<div class="footer">
  <span>PPDB Terpusat YPFC &copy; {{ date('Y') }}</span>
  <span>Dokumen digenerate otomatis oleh sistem</span>
</div>
</body>
</html>
