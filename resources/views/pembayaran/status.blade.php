@extends('layouts.app')
@section('title', 'Status Pembayaran - PPDB Yayasan Fatahillah')

@section('content')

@php
  $statusCfg = [
    'menunggu_verifikasi' => [
      'bg'    => 'bg-yellow-50 border-yellow-200',
      'text'  => 'text-yellow-700',
      'icon'  => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
      'ic'    => 'text-yellow-500',
      'label' => 'Menunggu Verifikasi Admin',
      'desc'  => 'Bukti pembayaran Anda sudah kami terima dan sedang dalam proses verifikasi oleh admin.',
    ],
    'sukses' => [
      'bg'    => 'bg-teal-50 border-teal-200',
      'text'  => 'text-teal-700',
      'icon'  => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
      'ic'    => 'text-teal-500',
      'label' => 'Pembayaran Lunas ✓',
      'desc'  => 'Pembayaran telah dikonfirmasi oleh admin. Silakan datang ke sekolah untuk konfirmasi dan melanjutkan proses pendaftaran lainnya.',
    ],
    'gagal' => [
      'bg'    => 'bg-red-50 border-red-200',
      'text'  => 'text-red-700',
      'icon'  => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
      'ic'    => 'text-red-500',
      'label' => 'Pembayaran Gagal',
      'desc'  => 'Pembayaran tidak dapat diverifikasi. Silakan hubungi admin untuk informasi lebih lanjut.',
    ],
    'kadaluarsa' => [
      'bg'    => 'bg-gray-50 border-gray-200',
      'text'  => 'text-gray-600',
      'icon'  => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
      'ic'    => 'text-gray-400',
      'label' => 'Kadaluarsa',
      'desc'  => 'Pembayaran ini telah kadaluarsa. Silakan hubungi admin.',
    ],
  ];

  $sCfg = $pembayaran ? ($statusCfg[$pembayaran->status_pembayaran] ?? $statusCfg['menunggu_verifikasi']) : null;

  $waMsg = urlencode(
    "Halo Admin PPDB, saya ingin konfirmasi pembayaran.\n" .
    "Kode: {$pendaftaran->kode_regis}\n" .
    "Nama: " . ($pendaftaran->siswa->nama_siswa ?? '-')
  );
  $waUrl = "https://wa.me/{$waAdmin}?text={$waMsg}";
@endphp

<section class="bg-gradient-to-br from-primary-600 to-primary-800 pt-32 pb-20 px-6 text-white text-center">
  <div class="max-w-3xl mx-auto">
    <span class="inline-block bg-white/10 border border-white/20 text-sm font-medium px-4 py-1.5 rounded-full mb-4">🔍 Status Pembayaran</span>
    <h1 class="text-3xl md:text-4xl font-extrabold mb-3">Status Pembayaran</h1>
    <p class="text-white/80 font-mono text-lg tracking-widest">{{ $pendaftaran->kode_regis }}</p>
  </div>
</section>

<section class="max-w-2xl mx-auto px-6 -mt-10 relative z-10 mb-20 space-y-5">

  @if(session('success'))
  <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-2xl">
    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span class="text-sm font-semibold">{{ session('success') }}</span>
  </div>
  @endif

  {{-- Status Pembayaran --}}
  <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">

    @if($pembayaran && $sCfg)
    {{-- Status Header --}}
    <div class="{{ $sCfg['bg'] }} border px-6 py-5 flex items-center gap-4">
      <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm shrink-0">
        <svg class="w-7 h-7 {{ $sCfg['ic'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sCfg['icon'] }}"/>
        </svg>
      </div>
      <div>
        <p class="text-xs font-bold {{ $sCfg['text'] }} uppercase tracking-wide mb-0.5">Status Pembayaran</p>
        <p class="text-lg font-extrabold {{ $sCfg['text'] }}">{{ $sCfg['label'] }}</p>
        <p class="text-xs {{ $sCfg['text'] }} opacity-80 mt-0.5">{{ $sCfg['desc'] }}</p>
      </div>
    </div>

    <div class="p-6 space-y-5">

      {{-- Detail Pembayaran --}}
      <div>
        <h3 class="text-sm font-bold text-gray-800 mb-3">Detail Pembayaran</h3>
        <div class="grid grid-cols-2 gap-3">
          @foreach([
            ['Kode Pendaftaran', $pendaftaran->kode_regis, true],
            ['Nama Siswa', $pendaftaran->siswa->nama_siswa ?? '-', false],
            ['Sekolah', $pendaftaran->sekolah->nama_sekolah ?? '-', false],
            ['Metode Bayar', ucfirst($pembayaran->metodePembayaran->nama_metode ?? '-'), false],
            ['Nominal', 'Rp ' . number_format($pembayaran->nominal, 0, ',', '.'), false],
            ['Tanggal Bayar', $pembayaran->tanggal_pembayaran ? \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->translatedFormat('d F Y') : '-', false],
          ] as [$lbl, $val, $mono])
          <div class="flex flex-col gap-0.5 p-3 bg-gray-50 rounded-xl">
            <span class="text-xs text-gray-400 font-medium">{{ $lbl }}</span>
            <span class="text-sm font-bold text-gray-900 {{ $mono ? 'font-mono tracking-wider' : '' }}">{{ $val }}</span>
          </div>
          @endforeach
        </div>
      </div>

      {{-- Bukti Bayar --}}
      @if($pembayaran->proof_path)
      <div class="pt-4 border-t border-gray-100">
        <h3 class="text-sm font-bold text-gray-800 mb-3">Bukti Pembayaran</h3>
        @php $ext = pathinfo($pembayaran->proof_path, PATHINFO_EXTENSION); @endphp
        @if(in_array(strtolower($ext), ['jpg','jpeg','png']))
        <div class="rounded-xl overflow-hidden border border-gray-200">
          <img src="{{ asset('storage/' . $pembayaran->proof_path) }}" alt="Bukti Bayar" class="w-full max-h-64 object-contain bg-gray-50">
        </div>
        @else
        <a href="{{ asset('storage/' . $pembayaran->proof_path) }}" target="_blank"
           class="flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 font-medium hover:bg-red-100 transition-colors">
          <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a2 2 0 002 2h4a2 2 0 002-2V3a2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd"/></svg>
          Lihat Bukti Pembayaran (PDF) ↗
        </a>
        @endif
      </div>
      @endif

      {{-- Info menunggu verifikasi --}}
      @if($pembayaran->status_pembayaran === 'menunggu_verifikasi')
      <div class="pt-4 border-t border-gray-100 bg-yellow-50 rounded-2xl p-4">
        <p class="text-sm font-bold text-yellow-800 mb-1">⏳ Menunggu Konfirmasi Admin</p>
        <p class="text-xs text-yellow-700 leading-relaxed">Proses verifikasi membutuhkan waktu <strong>1×24 jam kerja</strong>. Untuk mempercepat, Anda dapat menghubungi admin langsung melalui WhatsApp di bawah.</p>
      </div>
      @endif

      {{-- Sukses --}}
      @if($pembayaran->status_pembayaran === 'sukses')
      <div class="pt-4 border-t border-gray-100">

        {{-- Tombol Download PDF --}}
        <a href="{{ route('pembayaran.downloadPdf', ['kode' => $pendaftaran->kode_regis]) }}"
           target="_blank"
           class="flex items-center justify-center gap-2.5 w-full bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold py-3.5 rounded-2xl transition-all mb-2 text-sm hover:shadow-lg">
          <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          📄 Download Formulir Pendaftaran (PDF)
        </a>
        <p class="text-xs text-gray-400 text-center mb-4">Cetak & bawa ke sekolah sebagai bukti pendaftaran lunas</p>

        <div class="bg-teal-50 border border-teal-200 rounded-2xl p-5">
          <p class="text-sm font-bold text-teal-800 mb-3">🎉 Pembayaran Lunas — Langkah Selanjutnya</p>
          <div class="space-y-2.5">
            <div class="flex items-start gap-3 text-xs text-teal-700">
              <span class="w-5 h-5 bg-teal-600 text-white rounded-full flex items-center justify-center font-bold shrink-0 mt-0.5 text-[10px]">1</span>
              <span><strong>Download & cetak</strong> Formulir Pendaftaran di atas sebagai bukti resmi pembayaran lunas.</span>
            </div>
            <div class="flex items-start gap-3 text-xs text-teal-700">
              <span class="w-5 h-5 bg-teal-600 text-white rounded-full flex items-center justify-center font-bold shrink-0 mt-0.5 text-[10px]">2</span>
              <span>Datang ke <strong>{{ $pendaftaran->sekolah->nama_sekolah ?? 'sekolah tujuan' }}</strong> dengan membawa formulir beserta dokumen asli (Ijazah, KK, Akta Kelahiran).</span>
            </div>
            <div class="flex items-start gap-3 text-xs text-teal-700">
              <span class="w-5 h-5 bg-teal-600 text-white rounded-full flex items-center justify-center font-bold shrink-0 mt-0.5 text-[10px]">3</span>
              <span>Tunjukkan nomor <strong class="font-mono">{{ $pendaftaran->kode_regis }}</strong> kepada panitia PPDB dan ikuti arahan selanjutnya.</span>
            </div>
          </div>
          @if($pembayaran->verifikasi_tanggal)
          <p class="text-xs text-teal-500 mt-3 pt-3 border-t border-teal-200">
            Dikonfirmasi: {{ \Carbon\Carbon::parse($pembayaran->verifikasi_tanggal)->translatedFormat('d F Y, H:i') }} WIB
          </p>
          @endif
        </div>
      </div>
      @endif

    </div>

    @else
    {{-- Belum ada pembayaran --}}
    <div class="p-8 text-center">
      <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
      </div>
      <p class="font-bold text-gray-900 mb-1">Belum Ada Pembayaran</p>
      <p class="text-sm text-gray-500 mb-5">Belum ada data pembayaran untuk nomor pendaftaran <strong>{{ $pendaftaran->kode_regis }}</strong>.</p>
      @if(in_array($pendaftaran->status, ['diterima', 'menunggu_pembayaran']))
      <form method="POST" action="{{ route('pembayaran.cek') }}">
        @csrf
        <input type="hidden" name="kode_registrasi" value="{{ $pendaftaran->kode_regis }}">
        <button type="submit"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-primary-400 to-primary-600 text-white font-semibold px-6 py-3 rounded-2xl hover:shadow-lg hover:scale-105 transition-all text-sm">
          Bayar Sekarang →
        </button>
      </form>
      @endif
    </div>
    @endif

  </div>

  {{-- Action Buttons --}}
  <div class="bg-white rounded-2xl border border-gray-100 shadow px-6 py-5">
    <div class="flex flex-col sm:flex-row gap-3">
      {{-- WA Admin --}}
      <a href="{{ $waUrl }}" target="_blank"
         class="flex-1 flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1ebe5d] text-white font-semibold px-5 py-3 rounded-2xl transition-colors text-sm">
        <svg class="w-4 h-4 text-white shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        <span class="text-white">Konfirmasi via WhatsApp</span>
      </a>
      {{-- Cek Status Pendaftaran --}}
      <a href="{{ route('status.index') }}?kode={{ $pendaftaran->kode_regis }}"
         class="flex-1 flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold px-5 py-3 rounded-2xl transition-colors text-sm">
        <svg class="w-4 h-4 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <span class="text-white">Cek Status Pendaftaran</span>
      </a>
    </div>
  </div>

  {{-- Cek status pembayaran lain --}}
  <div class="text-center">
    <a href="{{ route('pembayaran.status.cek') }}" class="text-sm text-gray-500 hover:text-primary-600 transition-colors">
      🔍 Cek status pembayaran dengan kode lain
    </a>
  </div>

</section>

@endsection