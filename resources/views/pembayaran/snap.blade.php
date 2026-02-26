@extends('layouts.app')
@section('title', 'Lanjutkan Pembayaran - PPDB Yayasan Fatahillah')

@section('content')

<section class="bg-gradient-to-br from-primary-600 to-primary-800 pt-32 pb-20 px-6 text-white text-center">
  <div class="max-w-3xl mx-auto">
    <span class="inline-block bg-white/10 border border-white/20 text-sm font-medium px-4 py-1.5 rounded-full mb-4">⚡ Lanjutkan Pembayaran</span>
    <h1 class="text-3xl md:text-4xl font-extrabold mb-3">Selesaikan Pembayaran Midtrans</h1>
    <p class="text-white/80 font-mono text-lg tracking-widest">{{ $pembayaran->pendaftaran->kode_regis ?? '' }}</p>
  </div>
</section>

<section class="max-w-xl mx-auto px-6 -mt-10 relative z-10 mb-20">

  <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 p-8 text-center">

    <div class="w-20 h-20 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
      <svg class="w-10 h-10 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
      </svg>
    </div>

    <h2 class="text-xl font-extrabold text-gray-900 mb-2">Pembayaran Belum Selesai</h2>
    <p class="text-sm text-gray-500 mb-6">Kamu sudah memilih pembayaran Midtrans sebelumnya. Klik tombol di bawah untuk memunculkan kembali halaman pilihan metode bayar.</p>

    {{-- Info --}}
    <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-4 mb-6 text-left">
      <div class="flex items-center justify-between text-sm mb-2">
        <span class="text-gray-500">Nomor Pendaftaran</span>
        <span class="font-bold font-mono text-indigo-900">{{ $pembayaran->pendaftaran->kode_regis ?? '-' }}</span>
      </div>
      <div class="flex items-center justify-between text-sm mb-2">
        <span class="text-gray-500">Nama Siswa</span>
        <span class="font-bold text-gray-900">{{ $pembayaran->pendaftaran->siswa->nama_siswa ?? '-' }}</span>
      </div>
      <div class="flex items-center justify-between text-sm mb-2">
        <span class="text-gray-500">Nominal</span>
        <span class="font-bold text-gray-900">Rp 200.000</span>
      </div>
      <div class="flex items-center justify-between text-sm">
        <span class="text-gray-500">Status</span>
        <span class="font-bold text-yellow-600">⏳ Menunggu Pembayaran</span>
      </div>
    </div>

    <button id="btn-open-snap"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl transition-all flex items-center justify-center gap-2 text-sm hover:shadow-lg mb-3">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
      Buka Halaman Pembayaran Midtrans
    </button>

    <a href="{{ route('pembayaran.status', ['kode' => $pembayaran->pendaftaran->kode_regis ?? '']) }}"
       class="block text-sm text-gray-400 hover:text-primary-600 transition-colors mt-2">
      Lihat status pembayaran →
    </a>
  </div>

</section>

<script src="{{ $snapJsUrl }}" data-client-key="{{ $clientKey }}"></script>

@push('scripts')
<script>
  document.getElementById('btn-open-snap').addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = `<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Membuka...`;

    window.snap.pay('{{ $snapToken }}', {
      onSuccess: function(result) {
        window.location.href = '{{ $statusUrl }}?midtrans=success';
      },
      onPending: function(result) {
        window.location.href = '{{ $statusUrl }}?midtrans=pending';
      },
      onError: function(result) {
        Swal.fire({ icon: 'error', title: 'Pembayaran Gagal', text: 'Terjadi kesalahan. Silakan coba lagi.' });
        btn.disabled = false;
        btn.innerHTML = `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg> Buka Halaman Pembayaran Midtrans`;
      },
      onClose: function() {
        btn.disabled = false;
        btn.innerHTML = `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg> Buka Halaman Pembayaran Midtrans`;
      }
    });
  });

  // Auto-buka snap saat halaman load
  window.addEventListener('load', function() {
    setTimeout(() => {
      document.getElementById('btn-open-snap')?.click();
    }, 800);
  });
</script>
@endpush

@endsection
