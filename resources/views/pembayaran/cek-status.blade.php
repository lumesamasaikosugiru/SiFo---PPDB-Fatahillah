@extends('layouts.app')
@section('title', 'Cek Status Pembayaran - PPDB Yayasan Fatahillah')

@section('content')

<section class="bg-gradient-to-br from-primary-600 to-primary-800 pt-32 pb-20 px-6 text-white text-center">
  <div class="max-w-3xl mx-auto">
    <span class="inline-block bg-white/10 border border-white/20 text-sm font-medium px-4 py-1.5 rounded-full mb-4">🔍 Cek Status Pembayaran</span>
    <h1 class="text-3xl md:text-4xl font-extrabold mb-4">Cek Status Pembayaran</h1>
    <p class="text-white/80 text-lg max-w-xl mx-auto">Masukkan nomor pendaftaran untuk melihat status pembayaran uang pendaftaran Anda.</p>
  </div>
</section>

<section class="max-w-xl mx-auto px-6 -mt-10 relative z-10 mb-20">
  <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 p-8">

    @if($errors->any())
    <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl">
      <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <div>@foreach($errors->all() as $e)<p class="text-sm font-medium">{{ $e }}</p>@endforeach</div>
    </div>
    @endif

    <form method="GET" action="{{ route('pembayaran.status') }}" id="form-cek-status-bayar">
      <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Pendaftaran</label>
      <div class="flex gap-3">
        <div class="relative flex-1">
          <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
          </div>
          <input type="text" name="kode" id="input-kode"
                 placeholder="Contoh: PPDB26-AB3XY7KZ"
                 oninput="this.value=this.value.toUpperCase()"
                 class="w-full pl-12 pr-4 py-3.5 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-gray-50 transition-all font-mono tracking-wider"
                 required>
        </div>
        <button type="submit"
                class="bg-gradient-to-r from-primary-400 to-primary-600 text-white font-semibold px-6 py-3.5 rounded-2xl hover:shadow-lg hover:scale-105 transition-all text-sm whitespace-nowrap">
          Cek
        </button>
      </div>
      <p class="text-xs text-gray-400 mt-2 ml-1">Nomor pendaftaran dikirim ke email Anda saat mendaftar.</p>
    </form>

    <div class="mt-6 pt-5 border-t border-gray-100 flex justify-center gap-6">
      <a href="{{ route('pembayaran.index') }}" class="text-sm text-gray-500 hover:text-primary-600 transition-colors">
        💳 Lakukan Pembayaran
      </a>
      <a href="{{ route('status.index') }}" class="text-sm text-gray-500 hover:text-primary-600 transition-colors">
        📋 Cek Status Pendaftaran
      </a>
    </div>
  </div>
</section>

@endsection
