@extends('layouts.app')
@section('title', 'Lanjutkan Pembayaran Midtrans - PPDB Yayasan Fatahillah')

@section('content')

@php
  $kodeRegis  = $pembayaran->pendaftaran->kode_regis ?? '';
  $namaSiswa  = $pembayaran->pendaftaran->siswa->nama_siswa ?? '-';
  $namaSekolah = $pembayaran->pendaftaran->sekolah->nama_sekolah ?? '-';
  $statusUrl  = route('pembayaran.status', ['kode' => $kodeRegis]);
  $formUrl    = route('pembayaran.cek');
@endphp

{{-- Hero --}}
<section class="bg-gradient-to-br from-indigo-600 to-indigo-900 pt-32 pb-20 px-6 text-white text-center">
  <div class="max-w-3xl mx-auto">
    <span class="inline-block bg-white/10 border border-white/20 text-sm font-medium px-4 py-1.5 rounded-full mb-4">⏳ Pembayaran Belum Selesai</span>
    <h1 class="text-3xl md:text-4xl font-extrabold mb-3">Sesi Midtrans Masih Aktif</h1>
    <p class="text-white/80 font-mono text-lg tracking-widest">{{ $kodeRegis }}</p>
  </div>
</section>

<section class="max-w-xl mx-auto px-6 -mt-10 relative z-10 mb-20 space-y-5">

  {{-- Card Utama --}}
  <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">

    {{-- Header info --}}
    <div class="bg-indigo-50 border-b border-indigo-100 px-6 py-5">
      <div class="flex items-center gap-3 mb-1">
        <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        </div>
        <p class="font-bold text-indigo-900">Pembayaran Midtrans Belum Diselesaikan</p>
      </div>
      <p class="text-xs text-indigo-700 leading-relaxed">Kamu sudah memilih pembayaran Midtrans sebelumnya namun belum menyelesaikan proses pembayaran. Kamu bisa melanjutkan sesi ini atau membatalkan dan memilih ulang metode.</p>
    </div>

    {{-- Detail --}}
    <div class="px-6 py-5">
      <div class="grid grid-cols-2 gap-3 mb-5">
        @foreach([
          ['Nomor Pendaftaran', $kodeRegis, true],
          ['Nama Siswa', $namaSiswa, false],
          ['Sekolah Tujuan', $namaSekolah, false],
          ['Nominal', 'Rp 300.000', false],
          ['Status', 'Menunggu Pembayaran', false],
        ] as [$lbl, $val, $mono])
        <div class="flex flex-col gap-0.5 p-3 bg-gray-50 rounded-xl">
          <span class="text-xs text-gray-400 font-medium">{{ $lbl }}</span>
          <span class="text-sm font-bold text-gray-900 {{ $mono ? 'font-mono tracking-wider' : '' }}">{{ $val }}</span>
        </div>
        @endforeach
      </div>

      {{-- Tombol Lanjutkan --}}
      <button id="btn-open-snap"
              class="w-full bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold py-4 rounded-2xl transition-all flex items-center justify-center gap-2 text-sm hover:shadow-lg mb-3">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        ▶ Lanjutkan Pembayaran Midtrans
      </button>

      <p class="text-xs text-gray-400 text-center mb-5">Popup Midtrans akan terbuka — pilih metode yang Anda inginkan</p>

      {{-- Divider --}}
      <div class="flex items-center gap-3 mb-4">
        <div class="flex-1 h-px bg-gray-200"></div>
        <span class="text-xs text-gray-400 font-medium">atau</span>
        <div class="flex-1 h-px bg-gray-200"></div>
      </div>

      {{-- Reset / Pilih Ulang --}}
      <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
        <p class="text-xs font-bold text-red-800 mb-2">🔄 Mau pilih metode lain atau bayar manual?</p>
        <p class="text-xs text-red-700 mb-3 leading-relaxed">Reset sesi Midtrans ini dan kembali ke halaman pilih metode. Kamu bisa pilih Transfer Bank, Tunai, atau mulai Midtrans baru dari awal.</p>
        <form method="POST" action="{{ route('pembayaran.resetSnap', ['kode' => $kodeRegis]) }}" id="form-reset">
          @csrf
          <input type="hidden" name="kode" value="{{ $kodeRegis }}">
          <button type="button" id="btn-reset"
                  class="w-full flex items-center justify-center gap-2 bg-white hover:bg-red-100 text-red-700 font-semibold py-3 rounded-xl transition-all border border-red-300 text-sm hover:border-red-400">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            ✕ Batalkan & Pilih Metode Lain
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- Link ke status --}}
  <div class="text-center">
    <a href="{{ $statusUrl }}" class="text-sm text-gray-400 hover:text-primary-600 transition-colors">
      Lihat status pembayaran saya →
    </a>
  </div>

</section>

<script src="{{ $snapJsUrl }}" data-client-key="{{ $clientKey }}"></script>

@push('scripts')
<script>
  const SNAP_TOKEN  = '{{ $snapToken }}';
  const STATUS_URL  = '{{ $statusUrl }}';
  const SUKSES_URL  = '{{ route("pembayaran.sukses", ["kode" => $pembayaran->pendaftaran->kode_regis ?? ""]) }}';
  const SUCCESS_API = '{{ route("pembayaran.paymentSuccess") }}';
  const CSRF_TOKEN  = '{{ csrf_token() }}';
  const LANJUT_URL  = '{{ route("pembayaran.snapLanjut", ["kode" => $pembayaran->pendaftaran->kode_regis ?? ""]) }}';

  function resetSnapBtn() {
    const btn = document.getElementById('btn-open-snap');
    btn.disabled = false;
    btn.innerHTML = `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg> ▶ Lanjutkan Pembayaran Midtrans`;
  }

  function openSnap() {
    const btn = document.getElementById('btn-open-snap');
    btn.disabled = true;
    btn.innerHTML = `<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Membuka...`;

    window.snap.pay(SNAP_TOKEN, {
      onSuccess: function(result) {
        const btn = document.getElementById('btn-open-snap');
        btn.disabled = true;
        btn.innerHTML = `<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Memproses...`;
        fetch(SUCCESS_API, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
          body: JSON.stringify({
            snap_token:   SNAP_TOKEN,
            order_id:     result.order_id ?? '',
            payment_type: result.payment_type ?? 'midtrans',
          }),
        })
        .then(r => r.json())
        .then(res => { window.location.href = res.redirect_to ?? SUKSES_URL; })
        .catch(()  => { window.location.href = SUKSES_URL; });
      },
      onPending: function() {
        // Tetap di halaman ini, update tombol
        resetSnapBtn();
        Swal.fire({
          icon: 'info',
          title: 'Pembayaran Menunggu',
          text: 'Selesaikan pembayaran sesuai instruksi yang diberikan.',
          confirmButtonColor: '#4f46e5',
        });
      },
      onError: function() {
        resetSnapBtn();
        Swal.fire({ icon: 'error', title: 'Pembayaran Gagal', text: 'Terjadi kesalahan. Coba lagi atau pilih metode lain.' });
      },
      onClose: function() {
        resetSnapBtn();
      }
    });
  }

  // Tombol lanjutkan
  document.getElementById('btn-open-snap')?.addEventListener('click', openSnap);

  // Auto-buka snap setelah halaman load (750ms delay)
  window.addEventListener('load', function() {
    setTimeout(openSnap, 750);
  });

  // Tombol reset dengan konfirmasi SweetAlert
  document.getElementById('btn-reset')?.addEventListener('click', function() {
    Swal.fire({
      icon: 'question',
      title: 'Batalkan sesi Midtrans?',
      html: `<p class="text-sm text-gray-600">Sesi pembayaran Midtrans ini akan direset.<br>Kamu bisa pilih metode pembayaran lain (Transfer Bank, Tunai, atau Midtrans baru).</p>`,
      showCancelButton: true,
      confirmButtonText: '✕ Ya, Batalkan & Pilih Ulang',
      cancelButtonText: 'Tidak, Tetap di Sini',
      confirmButtonColor: '#dc2626',
      cancelButtonColor: '#6b7280',
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('form-reset').submit();
      }
    });
  });
</script>
@endpush

@endsection