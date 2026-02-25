@extends('layouts.app')
@section('title', 'Cek Status Pendaftaran - PPDB Yayasan Fatahillah')

@section('content')

<section class="bg-gradient-to-br from-primary-600 to-primary-800 pt-32 pb-20 px-6 text-white text-center">
  <div class="max-w-3xl mx-auto">
    <span class="inline-block bg-white/10 border border-white/20 text-sm font-medium px-4 py-1.5 rounded-full mb-4">📋 Cek Status Pendaftaran</span>
    <h1 class="text-3xl md:text-4xl font-extrabold mb-4">Cek Status Pendaftaran</h1>
    <p class="text-white/80 text-lg max-w-xl mx-auto">Masukkan nomor pendaftaran Anda untuk mengetahui status terkini proses seleksi PPDB 2026/2027.</p>
  </div>
</section>

<section class="max-w-3xl mx-auto px-6 -mt-10 relative z-10 mb-16">
  <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 p-8">

    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-2xl">
      <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl">
      <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <div>@foreach($errors->all() as $error)<p class="text-sm font-medium">{{ $error }}</p>@endforeach</div>
    </div>
    @endif

    <form method="POST" action="{{ route('status.check') }}" id="form-cek-status">
      @csrf
      <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Pendaftaran</label>
      <div class="flex gap-3">
        <div class="relative flex-1">
          <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          </div>
          <input type="text" name="kode_registrasi" id="input-kode" placeholder="Contoh: PPDB260001"
                 value="{{ old('kode_registrasi', isset($pendaftaran) ? $pendaftaran->kode_regis : '') }}"
                 style="text-transform:uppercase"
                 oninput="this.value=this.value.toUpperCase()"
                 class="w-full pl-12 pr-4 py-3.5 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-transparent transition-all bg-gray-50" required>
        </div>
        <button type="submit" class="bg-gradient-to-r from-primary-400 to-primary-600 text-white font-semibold px-6 py-3.5 rounded-2xl hover:shadow-lg hover:scale-105 transition-all text-sm whitespace-nowrap">Cek Status</button>
      </div>
      <p class="text-xs text-gray-400 mt-2 ml-1">Nomor pendaftaran dikirimkan ke email Anda setelah submit formulir.</p>
    </form>
  </div>
</section>

@if(isset($pendaftaran))
<section class="max-w-3xl mx-auto px-6 mb-20">
  @php
    $statusConfig = [
      'diproses'            => ['color'=>'bg-yellow-50 border-yellow-200 text-yellow-700','icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','iconColor'=>'text-yellow-500','label'=>'Sedang Diproses','desc'=>'Pendaftaran Anda sedang dalam proses verifikasi oleh panitia PPDB.'],
      'diverifikasi'        => ['color'=>'bg-blue-50 border-blue-200 text-blue-700','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4','iconColor'=>'text-blue-500','label'=>'Sedang Diverifikasi','desc'=>'Berkas Anda sedang diverifikasi oleh tim panitia PPDB.'],
      'diterima'            => ['color'=>'bg-green-50 border-green-200 text-green-700','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','iconColor'=>'text-green-500','label'=>'Diterima','desc'=>'Selamat! Anda dinyatakan diterima. Segera lakukan pembayaran uang pendaftaran.'],
      'ditolak'             => ['color'=>'bg-red-50 border-red-200 text-red-700','icon'=>'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z','iconColor'=>'text-red-500','label'=>'Tidak Diterima','desc'=>'Mohon maaf, Anda belum diterima pada periode PPDB ini.'],
      'menunggu_pembayaran' => ['color'=>'bg-orange-50 border-orange-200 text-orange-700','icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','iconColor'=>'text-orange-500','label'=>'Menunggu Pembayaran','desc'=>'Anda diterima! Segera selesaikan pembayaran uang pendaftaran.'],
      'pembayaran_diproses' => ['color'=>'bg-purple-50 border-purple-200 text-purple-700','icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','iconColor'=>'text-purple-500','label'=>'Pembayaran Diproses','desc'=>'Bukti pembayaran Anda sudah diterima dan sedang diverifikasi admin. Harap tunggu konfirmasi.'],
      'pembayaran_lunas'    => ['color'=>'bg-teal-50 border-teal-200 text-teal-700','icon'=>'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z','iconColor'=>'text-teal-500','label'=>'Pembayaran Lunas ✓','desc'=>'Pembayaran telah dikonfirmasi. Datang ke sekolah untuk konfirmasi dan melanjutkan proses pendaftaran.'],
      'selesai'             => ['color'=>'bg-teal-50 border-teal-200 text-teal-700','icon'=>'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z','iconColor'=>'text-teal-500','label'=>'Selesai 🎉','desc'=>'Pendaftaran selesai. Selamat bergabung sebagai siswa baru!'],
    ];
    $cfg = $statusConfig[$pendaftaran->status] ?? $statusConfig['diproses'];
  @endphp

  <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-6">
    {{-- Status Header --}}
    <div class="p-6 border-b border-gray-100 {{ $cfg['color'] }} border">
      <div class="flex items-center gap-4">
        <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm">
          <svg class="w-7 h-7 {{ $cfg['iconColor'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $cfg['icon'] }}"/></svg>
        </div>
        <div>
          <div class="text-xs font-medium opacity-70 mb-1">Status Pendaftaran</div>
          <div class="text-xl font-extrabold">{{ $cfg['label'] }}</div>
          <div class="text-sm opacity-80 mt-1">{{ $cfg['desc'] }}</div>
        </div>
      </div>
    </div>

    <div class="p-6">
      {{-- Informasi Pendaftaran --}}
      <h3 class="font-bold text-gray-900 mb-5 flex items-center gap-2 text-sm">
        <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Informasi Pendaftaran
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-2xl">
          <div class="w-9 h-9 bg-primary-100 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
          </div>
          <div>
            <div class="text-xs text-gray-500 font-medium mb-0.5">Nomor Pendaftaran</div>
            <div class="text-sm font-bold text-gray-900 font-mono">{{ $pendaftaran->kode_regis }}</div>
          </div>
        </div>
        <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-2xl">
          <div class="w-9 h-9 bg-primary-100 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          </div>
          <div>
            <div class="text-xs text-gray-500 font-medium mb-0.5">Nama Lengkap</div>
            <div class="text-sm font-semibold text-gray-900">{{ $pendaftaran->siswa->nama_siswa ?? '-' }}</div>
          </div>
        </div>
        <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-2xl">
          <div class="w-9 h-9 bg-primary-100 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
          </div>
          <div>
            <div class="text-xs text-gray-500 font-medium mb-0.5">Sekolah Tujuan</div>
            <div class="text-sm font-semibold text-gray-900">{{ $pendaftaran->sekolah->nama_sekolah ?? '-' }}</div>
          </div>
        </div>
        <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-2xl">
          <div class="w-9 h-9 bg-primary-100 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"/></svg>
          </div>
          <div>
            <div class="text-xs text-gray-500 font-medium mb-0.5">Jurusan</div>
            <div class="text-sm font-semibold text-gray-900">{{ $pendaftaran->jurusan->nama_jurusan ?? 'Tidak Ada (SMP)' }}</div>
          </div>
        </div>
        <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-2xl">
          <div class="w-9 h-9 bg-primary-100 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          </div>
          <div>
            <div class="text-xs text-gray-500 font-medium mb-0.5">Jalur Pendaftaran</div>
            <div class="text-sm font-semibold text-gray-900">{{ $pendaftaran->jalur_pendaftaran }}</div>
          </div>
        </div>
        <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-2xl">
          <div class="w-9 h-9 bg-primary-100 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          </div>
          <div>
            <div class="text-xs text-gray-500 font-medium mb-0.5">Tanggal Daftar</div>
            <div class="text-sm font-semibold text-gray-900">
              {{ $pendaftaran->tanggal_submit ? \Carbon\Carbon::parse($pendaftaran->tanggal_submit)->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}
            </div>
          </div>
        </div>
      </div>

      {{-- Data Siswa --}}
      @if($pendaftaran->siswa)
      <div class="mt-6 pt-6 border-t border-gray-100">
        <h4 class="font-bold text-gray-900 mb-4 text-sm flex items-center gap-2">
          <svg class="w-4 h-4 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          Data Siswa
        </h4>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
          @php $s = $pendaftaran->siswa; @endphp
          @foreach([
            ['NISN', $s->nisn ?? '-'],
            ['Jenis Kelamin', $s->jk ?? '-'],
            ['Tempat Lahir', $s->tempat_lahir ?? '-'],
            ['Tanggal Lahir', $s->tanggal_lahir ? \Carbon\Carbon::parse($s->tanggal_lahir)->translatedFormat('d F Y') : '-'],
            ['Agama', $s->agama ?? '-'],
            ['Email', $s->email ?? '-'],
            ['No. HP', $s->phone ?? '-'],
            ['Asal Sekolah', $s->asal_sekolah ?? '-'],
            ['Tahun Lulus', $s->tahun_lulus ?? '-'],
          ] as [$lbl, $val])
          <div class="flex flex-col gap-0.5 p-3 bg-gray-50 rounded-xl">
            <span class="text-xs text-gray-500">{{ $lbl }}</span>
            <span class="text-sm font-semibold text-gray-900">{{ $val }}</span>
          </div>
          @endforeach
        </div>
      </div>
      @endif

      {{-- Data Wali --}}
      @if($pendaftaran->waliSiswas && $pendaftaran->waliSiswas->count() > 0)
      <div class="mt-6 pt-6 border-t border-gray-100">
        <h4 class="font-bold text-gray-900 mb-4 text-sm flex items-center gap-2">
          <svg class="w-4 h-4 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          Data Orang Tua / Wali
        </h4>
        <div class="space-y-3">
          @foreach($pendaftaran->waliSiswas as $wali)
          <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-4 bg-gray-50 rounded-xl">
            <div><div class="text-xs text-gray-500">Nama</div><div class="text-sm font-semibold text-gray-900">{{ $wali->nama_wali }}</div></div>
            <div><div class="text-xs text-gray-500">Hubungan</div><div class="text-sm font-semibold text-gray-900 capitalize">{{ str_replace('_',' ',$wali->hubungan) }}</div></div>
            <div><div class="text-xs text-gray-500">Pekerjaan</div><div class="text-sm font-semibold text-gray-900">{{ $wali->pekerjaan }}</div></div>
            <div><div class="text-xs text-gray-500">No. Telepon</div><div class="text-sm font-semibold text-gray-900">{{ $wali->notelp_wali ?? '-' }}</div></div>
          </div>
          @endforeach
        </div>
      </div>
      @endif

      {{-- Dokumen --}}
      @if($pendaftaran->dokumens && $pendaftaran->dokumens->count() > 0)
      <div class="mt-6 pt-6 border-t border-gray-100">
        <h4 class="font-bold text-gray-900 mb-4 text-sm flex items-center gap-2">
          <svg class="w-4 h-4 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          Dokumen Terupload
        </h4>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
          @foreach($pendaftaran->dokumens as $dok)
          <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-100 rounded-xl">
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center shrink-0">
              <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
              <div class="text-xs font-semibold text-green-800 capitalize">{{ str_replace('_', ' ', $dok->tipe_dokumen) }}</div>
              <div class="text-xs text-green-600">Terupload</div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
      @endif

      {{-- Action Buttons --}}
      <div class="mt-8 pt-6 border-t border-gray-100 flex flex-wrap gap-3">

        {{-- Tombol Bayar — muncul jika status diterima atau menunggu_pembayaran --}}
        @if(in_array($pendaftaran->status, ['diterima', 'menunggu_pembayaran']))
        <form method="POST" action="{{ route('pembayaran.cek') }}" class="inline">
          @csrf
          <input type="hidden" name="kode_registrasi" value="{{ $pendaftaran->kode_regis }}">
          <button type="submit"
             class="inline-flex items-center gap-2 bg-gradient-to-r from-primary-400 to-primary-600 text-white font-semibold px-6 py-3 rounded-2xl hover:shadow-lg hover:scale-105 transition-all text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
            💳 Bayar Uang Pendaftaran
          </button>
        </form>
        @endif

        {{-- Tombol Cek Status Pembayaran — muncul jika sudah ada proses pembayaran --}}
        @if(in_array($pendaftaran->status, ['pembayaran_diproses', 'pembayaran_lunas', 'selesai']))
        <a href="{{ route('pembayaran.status', ['kode' => $pendaftaran->kode_regis]) }}"
           class="inline-flex items-center gap-2 bg-purple-600 text-white font-semibold px-6 py-3 rounded-2xl hover:shadow-lg hover:scale-105 transition-all text-sm">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          🔍 Cek Status Pembayaran
        </a>
        @endif

        <a href="{{ route('status.index') }}" class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 font-semibold px-6 py-3 rounded-2xl hover:bg-gray-200 transition-all text-sm">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
          Cek Ulang
        </a>
        <a href="{{ route('daftar.create') }}" class="inline-flex items-center gap-2 border border-primary-300 text-primary-600 font-semibold px-6 py-3 rounded-2xl hover:bg-primary-50 transition-all text-sm">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
          Pendaftaran Baru
        </a>
      </div>
    </div>
  </div>
</section>

@else
{{-- Empty State --}}
<section class="max-w-3xl mx-auto px-6 mb-20">
  <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-12 text-center">
    <div class="w-20 h-20 bg-gray-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
      <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    </div>
    <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Data</h3>
    <p class="text-gray-500 text-sm mb-8 max-w-sm mx-auto">Masukkan nomor pendaftaran Anda di form di atas untuk melihat status pendaftaran.</p>
    <div class="bg-gray-50 rounded-2xl p-5 text-left max-w-sm mx-auto">
      <p class="text-xs font-semibold text-gray-700 mb-3">Contoh format nomor pendaftaran:</p>
      <div class="space-y-2">
        <div class="flex items-center gap-2">
          <span class="w-2 h-2 bg-primary-400 rounded-full"></span>
          <code class="text-xs text-primary-600 font-mono bg-primary-50 px-2 py-1 rounded-lg">PPDB260001</code>
        </div>
        <div class="flex items-center gap-2">
          <span class="w-2 h-2 bg-primary-400 rounded-full"></span>
          <code class="text-xs text-primary-600 font-mono bg-primary-50 px-2 py-1 rounded-lg">PPDB260002</code>
        </div>
      </div>
      <p class="text-xs text-gray-400 mt-3">Nomor pendaftaran dikirim ke email Anda setelah submit formulir.</p>
    </div>
  </div>
</section>
@endif

@push('scripts')
<script>
  // Auto-isi dan auto-submit dari query param ?kode=PPDB260001
  (function() {
    const kode = new URLSearchParams(window.location.search).get('kode');
    if (!kode) return;
    const input = document.getElementById('input-kode');
    if (input && !input.value) {
      input.value = kode.toUpperCase();
      document.getElementById('form-cek-status').submit();
    }
  })();
</script>
@endpush

@endsection