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
.sum-val { font-size: 13px; font-weight: bold; color: #15803d; }
.sum-lbl { font-size: 7.5px; color: #666; margin-top: 2px; }
.divider { border: none; border-top: 1px solid #e5e7eb; margin: 4px 18px 6px; }
table { width: calc(100% - 36px); margin: 0 18px; border-collapse: collapse; font-size: 8.5px; }
thead tr { background: #16a34a; color: #fff; }
thead th { padding: 5px 6px; text-align: left; font-weight: bold; white-space: nowrap; }
tbody tr:nth-child(even) { background: #f0fdf4; }
tbody tr { border-bottom: .5px solid #e5e7eb; }
tbody td { padding: 4px 6px; vertical-align: middle; }
.badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 7.5px; font-weight: bold; white-space: nowrap; }
.s-diproses { background:#fef9c3; color:#854d0e; }
.s-diverifikasi { background:#dbeafe; color:#1e3a8a; }
.s-diterima { background:#dcfce7; color:#14532d; }
.s-ditolak { background:#fee2e2; color:#7f1d1d; }
.s-menunggu_pembayaran { background:#ffedd5; color:#7c2d12; }
.s-pembayaran_diproses { background:#e0e7ff; color:#312e81; }
.s-pembayaran_lunas { background:#d1fae5; color:#064e3b; }
.s-selesai { background:#f3f4f6; color:#374151; }
.footer { margin-top: 12px; padding: 6px 18px; font-size: 7.5px; color: #9ca3af; border-top: 1px solid #e5e7eb; display: table; width: 100%; }
.footer span:last-child { float: right; }
</style>
</head>
<body>
<div class="header">
  <h1>PPDB Terpusat YPFC &mdash; Laporan Data Pendaftaran</h1>
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
<table style="margin: 6px 18px 4px; width: calc(100% - 36px); border-collapse: collapse; border: 1px solid #bbf7d0; border-radius: 4px; font-size: 9px;">
  <tr>
    <td style="width:25%; padding:8px 10px; background:#f0fdf4; border-right:1px solid #bbf7d0; vertical-align:middle;">
      <div class="sum-val">{{ count($records) }}</div>
      <div class="sum-lbl">Total Pendaftar</div>
    </td>
    <td style="width:25%; padding:8px 10px; background:#f0fdf4; border-right:1px solid #bbf7d0; vertical-align:middle;">
      <div class="sum-val">{{ collect($records)->where('status','diproses')->count() }}</div>
      <div class="sum-lbl">Diproses</div>
    </td>
    <td style="width:25%; padding:8px 10px; background:#f0fdf4; border-right:1px solid #bbf7d0; vertical-align:middle;">
      <div class="sum-val">{{ collect($records)->where('status','diterima')->count() }}</div>
      <div class="sum-lbl">Diterima</div>
    </td>
    <td style="width:25%; padding:8px 10px; background:#f0fdf4; vertical-align:middle;">
      <div class="sum-val">{{ collect($records)->whereIn('status',['pembayaran_lunas','selesai'])->count() }}</div>
      <div class="sum-lbl">Lunas / Selesai</div>
    </td>
  </tr>
</table>
<hr class="divider">

<table>
  <thead>
    <tr>
      <th width="20">No</th>
      <th>Kode Regis</th>
      <th>Tahun</th>
      <th>Sekolah Tujuan</th>
      <th>Jurusan</th>
      <th>NISN</th>
      <th>Nama Siswa</th>
      <th>JK</th>
      <th>Asal Sekolah</th>
      <th>Jalur</th>
      <th>Status</th>
      <th>Tgl Submit</th>
      <th>Verifikator</th>
    </tr>
  </thead>
  <tbody>
    @forelse($records as $i => $row)
    <tr>
      <td style="text-align:center">{{ $i+1 }}</td>
      <td>{{ $row->kode_regis }}</td>
      <td>{{ $row->tahunAkademik?->tahun ?? '-' }}</td>
      <td>{{ $row->sekolah?->nama_sekolah ?? '-' }}</td>
      <td>{{ $row->jurusan?->nama_jurusan ?? '-' }}</td>
      <td>{{ $row->siswa?->nisn ?? '-' }}</td>
      <td>{{ $row->siswa?->nama_siswa ?? '-' }}</td>
      <td>{{ $row->siswa?->jk === 'laki_laki' ? 'L' : 'P' }}</td>
      <td>{{ $row->siswa?->asal_sekolah ?? '-' }}</td>
      <td>{{ ucfirst($row->jalur_pendaftaran ?? '-') }}</td>
      <td>
        @php
          $sl = ['diproses'=>'Diproses','diverifikasi'=>'Diverifikasi','diterima'=>'Diterima','ditolak'=>'Ditolak','menunggu_pembayaran'=>'Menunggu Bayar','pembayaran_diproses'=>'Bayar Diproses','pembayaran_lunas'=>'Lunas','selesai'=>'Selesai'];
        @endphp
        <span class="badge s-{{ $row->status }}">{{ $sl[$row->status] ?? ucfirst($row->status) }}</span>
      </td>
      <td>{{ $row->tanggal_submit?->format('d/m/Y') ?? '-' }}</td>
      <td>{{ $row->userVerifikator?->name ?? '-' }}</td>
    </tr>
    @empty
    <tr><td colspan="13" style="text-align:center;padding:12px;color:#9ca3af;">Tidak ada data</td></tr>
    @endforelse
  </tbody>
</table>

<div class="footer">
  <span>PPDB Terpusat YPFC &copy; {{ date('Y') }}</span>
  <span>Dokumen digenerate otomatis oleh sistem</span>
</div>
</body>
</html>
