@extends('layouts.app')
@section('title', 'Informasi Pembayaran - PPDB Yayasan Fatahillah')

@section('content')

@php
  $isSuccess = in_array($transactionStatus, ['settlement', 'capture']) || $pembayaran->status_pembayaran === 'sukses';
  $isPending  = $transactionStatus === 'pending' || $pembayaran->status_pembayaran === 'pending';
  $isFailed   = in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure']) || in_array($pembayaran->status_pembayaran, ['gagal', 'kadaluarsa']);

  $waMsg = urlencode("Halo Admin PPDB, saya sudah bayar.\nKode: {$pendaftaran->kode_regis}\nNama: " . ($pendaftaran->siswa->nama_siswa ?? '-'));
  $waUrl = "https://wa.me/{$waAdmin}?text={$waMsg}";
@endphp

<section class="bg-gradient-to-br {{ $isSuccess ? 'from-teal-600 to-teal-800' : ($isPending ? 'from-blue-600 to-blue-800' : 'from-red-600 to-red-800') }} pt-32 pb-20 px-6 text-white text-center">
  <div class="max-w-3xl mx-auto">
    <span class="text-5xl mb-4 block">{{ $isSuccess ? '🎉' : ($isPending ? '⏳' : '❌') }}</span>
    <h1 class="text-3xl md:text-4xl font-extrabold mb-3">
      {{ $isSuccess ? 'Pembayaran Berhasil!' : ($isPending ? 'Menunggu Pembayaran' : 'Pembayaran Gagal') }}
    </h1>
    <p class="text-white/80 font-mono text-lg tracking-widest">{{ $pendaftaran->kode_regis }}</p>
  </div>
</section>

<section class="max-w-xl mx-auto px-6 -mt-10 relative z-10 mb-20 space-y-5">

  {{-- Status Card --}}
  <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
    @if($isSuccess)
    <div class="bg-teal-50 border-b border-teal-200 px-6 py-5">
      <p class="text-base font-bold text-teal-800">✅ Pembayaran Lunas & Pendaftaran Selesai</p>
      <p class="text-xs text-teal-700 mt-1">Pembayaran berhasil dikonfirmasi otomatis. Email konfirmasi dan dokumen PDF sudah dikirim ke email Anda.</p>
    </div>
    @elseif($isPending)
    <div class="bg-blue-50 border-b border-blue-200 px-6 py-5">
      <p class="text-base font-bold text-blue-800">⏳ Menunggu Pembayaran</p>
      <p class="text-xs text-blue-700 mt-1">Anda sudah memilih metode pembayaran. Segera selesaikan pembayaran sebelum batas waktu habis.</p>
    </div>
    @else
    <div class="bg-red-50 border-b border-red-200 px-6 py-5">
      <p class="text-base font-bold text-red-800">❌ Pembayaran Tidak Berhasil</p>
      <p class="text-xs text-red-700 mt-1">Pembayaran gagal, dibatalkan, atau kadaluarsa. Silakan coba lagi.</p>
    </div>
    @endif

    <div class="p-6 space-y-3">
      <div class="grid grid-cols-2 gap-3">
        @foreach([
          ['Kode Pendaftaran', $pendaftaran->kode_regis, true],
          ['Nama Siswa', $pendaftaran->siswa->nama_siswa ?? '-', false],
          ['Sekolah', $pendaftaran->sekolah->nama_sekolah ?? '-', false],
          ['Nominal', 'Rp 200.000', false],
          ['Status Bayar', $pembayaran->labelStatus ?? ucfirst($pembayaran->status_pembayaran), false],
        ] as [$lbl, $val, $mono])
        <div class="flex flex-col gap-0.5 p-3 bg-gray-50 rounded-xl">
          <span class="text-xs text-gray-400 font-medium">{{ $lbl }}</span>
          <span class="text-sm font-bold text-gray-900 {{ $mono ? 'font-mono tracking-wider' : '' }}">{{ $val }}</span>
        </div>
        @endforeach
      </div>

      @if($isSuccess)
      <div class="bg-teal-50 border border-teal-200 rounded-2xl p-4 mt-2">
        <p class="text-sm font-bold text-teal-800 mb-3">📌 Langkah Selanjutnya:</p>
        <div class="space-y-2">
          <div class="flex items-start gap-3 text-xs text-teal-700">
            <span class="w-5 h-5 bg-teal-600 text-white rounded-full flex items-center justify-center font-bold shrink-0 mt-0.5 text-[10px]">1</span>
            <span>Cek email Anda untuk dokumen PDF bukti pembayaran yang sudah dikirim otomatis.</span>
          </div>
          <div class="flex items-start gap-3 text-xs text-teal-700">
            <span class="w-5 h-5 bg-teal-600 text-white rounded-full flex items-center justify-center font-bold shrink-0 mt-0.5 text-[10px]">2</span>
            <span>Datang ke <strong>{{ $pendaftaran->sekolah->nama_sekolah ?? 'sekolah tujuan' }}</strong> dengan membawa nomor pendaftaran <strong class="font-mono">{{ $pendaftaran->kode_regis }}</strong>.</span>
          </div>
          <div class="flex items-start gap-3 text-xs text-teal-700">
            <span class="w-5 h-5 bg-teal-600 text-white rounded-full flex items-center justify-center font-bold shrink-0 mt-0.5 text-[10px]">3</span>
            <span>Serahkan dokumen asli (Ijazah, KK, Akta) dan ikuti arahan panitia PPDB.</span>
          </div>
        </div>
      </div>
      @elseif($isPending)
      <a href="{{ route('pembayaran.snapPage', ['snapToken' => $pembayaran->snap_token]) }}"
         class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-2xl transition-colors mt-2 text-sm">
        Selesaikan Pembayaran →
      </a>
      @else
      <a href="{{ route('pembayaran.cek') }}?kode={{ $pendaftaran->kode_regis }}"
         class="block w-full text-center bg-primary-600 hover:bg-primary-700 text-white font-bold py-3.5 rounded-2xl transition-colors mt-2 text-sm">
        Coba Bayar Lagi →
      </a>
      @endif
    </div>
  </div>

  {{-- Action --}}
  <div class="bg-white rounded-2xl border border-gray-100 shadow px-6 py-5 flex flex-col sm:flex-row gap-3">
    <a href="{{ $waUrl }}" target="_blank"
       class="flex-1 flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1ebe5d] text-white font-semibold px-5 py-3 rounded-2xl transition-colors text-sm">
      <svg class="w-4 h-4 text-white shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
      <span class="text-white">Konfirmasi via WA Admin</span>
    </a>
    <a href="{{ route('pembayaran.status', ['kode' => $pendaftaran->kode_regis]) }}"
       class="flex-1 flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold px-5 py-3 rounded-2xl transition-colors text-sm">
      <span class="text-white">Lihat Status Pembayaran</span>
    </a>
  </div>

</section>

@endsection
