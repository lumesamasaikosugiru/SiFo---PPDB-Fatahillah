@extends('layouts.app')
@section('title', 'Pembayaran Pendaftaran - PPDB Yayasan Fatahillah')

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-primary-600 to-primary-800 pt-32 pb-20 px-6 text-white text-center">
  <div class="max-w-3xl mx-auto">
    <span class="inline-block bg-white/10 border border-white/20 text-sm font-medium px-4 py-1.5 rounded-full mb-4">💳 Pembayaran PPDB</span>
    <h1 class="text-3xl md:text-4xl font-extrabold mb-4">Pembayaran Uang Pendaftaran</h1>
    <p class="text-white/80 text-lg max-w-xl mx-auto">Masukkan nomor pendaftaran Anda untuk melanjutkan proses pembayaran PPDB 2026/2027.</p>
  </div>
</section>

<section class="max-w-xl mx-auto px-6 -mt-10 relative z-10 mb-20">

  <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">

    {{-- Nominal banner --}}
    <div class="bg-gradient-to-r from-primary-500 to-teal-500 px-8 py-5 flex items-center justify-between">
      <div>
        <p class="text-white/80 text-xs font-medium uppercase tracking-wide">Biaya Pendaftaran</p>
        <p class="text-white text-3xl font-extrabold mt-0.5">Rp 300.000</p>
      </div>
      <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center">
        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
        </svg>
      </div>
    </div>

    <div class="p-8">

      @if($errors->any())
      <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl">
        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>@foreach($errors->all() as $e)<p class="text-sm font-medium">{{ $e }}</p>@endforeach</div>
      </div>
      @endif

      <form method="POST" action="{{ route('pembayaran.cek') }}" id="form-cek">
        @csrf
        <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Pendaftaran</label>
        <div class="flex gap-3">
          <div class="relative flex-1">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
            </div>
            <input type="text" name="kode_registrasi" id="input-kode"
                   placeholder="Contoh: PPDB260001"
                   value="{{ old('kode_registrasi') }}"
                   oninput="this.value=this.value.toUpperCase()"
                   class="w-full pl-12 pr-4 py-3.5 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-transparent bg-gray-50 transition-all font-mono tracking-wider"
                   required>
          </div>
          <button type="submit"
                  class="bg-gradient-to-r from-primary-400 to-primary-600 text-white font-semibold px-6 py-3.5 rounded-2xl hover:shadow-lg hover:scale-105 transition-all text-sm whitespace-nowrap">
            Lanjutkan →
          </button>
        </div>
        <p class="text-xs text-gray-400 mt-2 ml-1">Nomor pendaftaran ada di email konfirmasi yang dikirim saat mendaftar.</p>
      </form>

      {{-- Syarat --}}
      <div class="mt-6 pt-6 border-t border-gray-100">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Siapa yang bisa bayar?</p>
        <div class="space-y-2">
          <div class="flex items-center gap-2.5 text-xs text-gray-600 bg-green-50 px-3 py-2.5 rounded-xl">
            <span class="text-green-500 text-base">✓</span>
            <span>Status pendaftaran <strong class="text-green-700">Diterima</strong></span>
          </div>
          <div class="flex items-center gap-2.5 text-xs text-gray-600 bg-green-50 px-3 py-2.5 rounded-xl">
            <span class="text-green-500 text-base">✓</span>
            <span>Status pendaftaran <strong class="text-green-700">Menunggu Pembayaran</strong></span>
          </div>
          <div class="flex items-center gap-2.5 text-xs text-gray-500 bg-gray-50 px-3 py-2.5 rounded-xl">
            <span class="text-gray-400 text-base">✕</span>
            <span>Status lain (diproses, diverifikasi, ditolak) belum bisa bayar</span>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="text-center mt-5 flex justify-center gap-6">
    <a href="{{ route('status.index') }}" class="text-sm text-gray-500 hover:text-primary-600 transition-colors">
      📋 Cek Status Pendaftaran
    </a>
    <a href="{{ route('pembayaran.status.cek') }}" class="text-sm text-gray-500 hover:text-primary-600 transition-colors">
      🔍 Cek Status Pembayaran
    </a>
  </div>

</section>

@push('scripts')
<script>
  // Auto-isi + auto-submit dari ?kode=
  (function () {
    const kode = new URLSearchParams(window.location.search).get('kode');
    if (!kode) return;
    const input = document.getElementById('input-kode');
    if (input && !input.value) {
      input.value = kode.toUpperCase();
      document.getElementById('form-cek').submit();
    }
  })();
</script>
@endpush

@endsection
