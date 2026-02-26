@extends('layouts.app')
@section('title', 'Pembayaran Berhasil! - PPDB Yayasan Fatahillah')

@section('content')

@php
  $kodeRegis   = $pendaftaran->kode_regis;
  $namaSiswa   = $pendaftaran->siswa->nama_siswa ?? '-';
  $namaSekolah = $pendaftaran->sekolah->nama_sekolah ?? '-';
  $namaJurusan = $pendaftaran->jurusan->nama_jurusan ?? 'Tidak Ada (SMP)';
  $jalur       = ucfirst($pendaftaran->jalur_pendaftaran ?? '-');
  $tanggalBayar = $pembayaran->verifikasi_tanggal
      ? \Carbon\Carbon::parse($pembayaran->verifikasi_tanggal)->translatedFormat('d F Y, H:i')
      : \Carbon\Carbon::now()->translatedFormat('d F Y, H:i');
  $namaMetode  = ucfirst($pembayaran->metodePembayaran->nama_metode ?? 'Midtrans');
  $waAdmin     = $waAdmin ?? '6208993388681';
  $waMsg = urlencode("Halo Admin PPDB, pembayaran saya sudah berhasil.\nKode: {$kodeRegis}\nNama: {$namaSiswa}");
  $waUrl = "https://wa.me/{$waAdmin}?text={$waMsg}";
  $downloadUrl = route('pembayaran.downloadPdf', ['kode' => $kodeRegis]);
  $statusUrl   = route('pembayaran.status', ['kode' => $kodeRegis]);
@endphp

{{-- Hero --}}
<section class="bg-gradient-to-br from-teal-600 via-teal-700 to-emerald-800 pt-32 pb-24 px-6 text-white text-center relative overflow-hidden">
  {{-- Confetti dots dekoratif --}}
  <div class="absolute inset-0 opacity-10 pointer-events-none select-none" aria-hidden="true">
    <div class="absolute top-10 left-10 w-4 h-4 bg-white rounded-full"></div>
    <div class="absolute top-20 right-20 w-3 h-3 bg-yellow-300 rounded-full"></div>
    <div class="absolute bottom-16 left-1/4 w-2 h-2 bg-green-300 rounded-full"></div>
    <div class="absolute top-16 left-1/2 w-3 h-3 bg-white rounded-full"></div>
    <div class="absolute bottom-10 right-1/4 w-4 h-4 bg-yellow-200 rounded-full"></div>
  </div>
  <div class="max-w-3xl mx-auto relative z-10">
    <div class="text-6xl mb-4">🎉</div>
    <span class="inline-block bg-white/15 border border-white/25 text-sm font-semibold px-4 py-1.5 rounded-full mb-4">✅ Pembayaran Berhasil</span>
    <h1 class="text-3xl md:text-4xl font-extrabold mb-3">Selamat! Pembayaran Lunas</h1>
    <p class="text-white/80 text-base max-w-lg mx-auto mb-4">Pembayaran PPDB Anda telah berhasil dikonfirmasi. Email konfirmasi beserta dokumen PDF sudah dikirim ke email Anda.</p>
    <p class="text-white/60 font-mono text-lg tracking-widest">{{ $kodeRegis }}</p>
  </div>
</section>

<section class="max-w-xl mx-auto px-6 -mt-10 relative z-10 mb-20 space-y-5">

  {{-- Card Utama --}}
  <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">

    {{-- Header card --}}
    <div class="bg-teal-50 border-b border-teal-100 px-6 py-5 flex items-center gap-3">
      <div class="w-10 h-10 bg-teal-600 rounded-xl flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
        </svg>
      </div>
      <div>
        <p class="font-bold text-teal-900 text-sm">Pembayaran Dikonfirmasi Otomatis</p>
        <p class="text-xs text-teal-700 mt-0.5">Lunas pada {{ $tanggalBayar }} WIB</p>
      </div>
    </div>

    <div class="px-6 py-5">

      {{-- Kode Pendaftaran highlight --}}
      <div class="border-2 border-teal-500 rounded-2xl p-4 text-center mb-5 bg-teal-50">
        <p class="text-xs text-teal-600 font-semibold uppercase tracking-widest mb-1">Nomor Pendaftaran</p>
        <p class="text-3xl font-extrabold font-mono tracking-widest text-teal-800">{{ $kodeRegis }}</p>
        <p class="text-xs text-teal-500 mt-1">Tunjukkan nomor ini kepada panitia PPDB</p>
      </div>

      {{-- Detail grid --}}
      <div class="grid grid-cols-2 gap-3 mb-5">
        @foreach([
          ['Nama Siswa',     $namaSiswa,   false],
          ['Sekolah Tujuan', $namaSekolah, false],
          ['Jurusan',        $namaJurusan, false],
          ['Jalur',          $jalur,       false],
          ['Metode Bayar',   $namaMetode,  false],
          ['Nominal',        'Rp 200.000', false],
        ] as [$lbl, $val, $mono])
        <div class="flex flex-col gap-0.5 p-3 bg-gray-50 rounded-xl">
          <span class="text-xs text-gray-400 font-medium">{{ $lbl }}</span>
          <span class="text-sm font-bold text-gray-900 {{ $mono ? 'font-mono' : '' }}">{{ $val }}</span>
        </div>
        @endforeach
      </div>

      {{-- Tombol Download PDF --}}
      <!-- <a href="{{ $downloadUrl }}" target="_blank"
         class="flex items-center justify-center gap-2.5 w-full bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-bold py-4 rounded-2xl transition-all mb-2 text-sm hover:shadow-lg"> -->
      <a href="{{ $downloadUrl }}" target="_blank"
        class="flex items-center justify-center gap-2.5 w-full bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold py-4 rounded-2xl transition-all mb-2 text-sm hover:shadow-lg">
        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        📄 Download Formulir Pendaftaran (PDF)
      </a>
      <p class="text-xs text-gray-400 text-center mb-5">Cetak & bawa ke sekolah sebagai bukti pendaftaran lunas</p>

      {{-- Langkah Selanjutnya --}}
      <div class="bg-teal-50 border border-teal-200 rounded-2xl p-4">
        <p class="text-xs font-bold text-teal-800 mb-3">📌 Langkah Selanjutnya</p>
        <div class="space-y-2.5">
          <div class="flex items-start gap-3 text-xs text-teal-700">
            <span class="w-5 h-5 bg-teal-600 text-white rounded-full flex items-center justify-center font-bold shrink-0 mt-0.5 text-[10px]">1</span>
            <span>Cek <strong>email Anda</strong> — dokumen PDF bukti pembayaran sudah dikirim otomatis.</span>
          </div>
          <div class="flex items-start gap-3 text-xs text-teal-700">
            <span class="w-5 h-5 bg-teal-600 text-white rounded-full flex items-center justify-center font-bold shrink-0 mt-0.5 text-[10px]">2</span>
            <span><strong>Download & cetak</strong> Formulir Pendaftaran di atas, atau gunakan yang di email.</span>
          </div>
          <div class="flex items-start gap-3 text-xs text-teal-700">
            <span class="w-5 h-5 bg-teal-600 text-white rounded-full flex items-center justify-center font-bold shrink-0 mt-0.5 text-[10px]">3</span>
            <span>Datang ke <strong>{{ $namaSekolah }}</strong> dengan membawa formulir + dokumen asli (Ijazah, KK, Akta Kelahiran).</span>
          </div>
          <div class="flex items-start gap-3 text-xs text-teal-700">
            <span class="w-5 h-5 bg-teal-600 text-white rounded-full flex items-center justify-center font-bold shrink-0 mt-0.5 text-[10px]">4</span>
            <span>Tunjukkan kode <strong class="font-mono">{{ $kodeRegis }}</strong> kepada panitia PPDB dan ikuti arahan selanjutnya.</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Action Buttons --}}
  <div class="bg-white rounded-2xl border border-gray-100 shadow px-6 py-5 flex flex-col sm:flex-row gap-3">
    <a href="{{ $waUrl }}" target="_blank"
       class="flex-1 flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1ebe5d] text-white font-semibold px-5 py-3 rounded-2xl transition-colors text-sm">
      <svg class="w-4 h-4 text-white shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
      <span class="text-white">Konfirmasi via WA</span>
    </a>
    <a href="{{ $statusUrl }}"
       class="flex-1 flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold px-5 py-3 rounded-2xl transition-colors text-sm">
      <svg class="w-4 h-4 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      <span class="text-white">Lihat Status Pembayaran</span>
    </a>
  </div>

</section>

@endsection