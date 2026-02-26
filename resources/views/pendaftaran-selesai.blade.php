@extends('layouts.app')
@section('title', 'Pendaftaran Berhasil - PPDB Yayasan Fatahillah')

@section('content')

{{-- Clear localStorage cache form daftar --}}
@push('scripts')
<script>
  // Reset cache form agar kosong saat buka /daftar lagi
  try { localStorage.removeItem('ppdb_form_cache_2026'); } catch(e) {}

  function salinKode(elId, btn) {
    const teks = document.getElementById(elId)?.textContent?.trim();
    if (!teks) return;
    navigator.clipboard.writeText(teks).then(() => {
      const iconCopy  = document.getElementById('icon-copy-' + elId);
      const iconCheck = document.getElementById('icon-check-' + elId);
      if (iconCopy)  iconCopy.classList.add('hidden');
      if (iconCheck) iconCheck.classList.remove('hidden');
      btn.classList.add('bg-white/40');
      setTimeout(() => {
        if (iconCopy)  iconCopy.classList.remove('hidden');
        if (iconCheck) iconCheck.classList.add('hidden');
        btn.classList.remove('bg-white/40');
      }, 2000);
    }).catch(() => {
      // Fallback untuk browser lama
      const ta = document.createElement('textarea');
      ta.value = teks; ta.style.position = 'fixed'; ta.style.opacity = '0';
      document.body.appendChild(ta); ta.select();
      document.execCommand('copy');
      document.body.removeChild(ta);
    });
  }
</script>
@endpush

{{-- Hero --}}
<section class="min-h-screen bg-gradient-to-br from-primary-700 via-primary-600 to-teal-500 flex items-center justify-center px-4 py-20">
  <div class="w-full max-w-2xl mt-10">

    {{-- Success Card --}}
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">

      {{-- Top accent --}}
      <div class="h-2 bg-gradient-to-r from-primary-400 via-teal-400 to-green-400"></div>

      <div class="p-8 md:p-12">

        {{-- Icon & Title --}}
        <div class="text-center mb-8">
          <div class="w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-5 border-4 border-green-100">
            <svg class="w-12 h-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-2">Pendaftaran Berhasil! 🎉</h1>
          <p class="text-gray-500 text-sm md:text-base max-w-md mx-auto">
            Data Anda telah kami terima. Simpan nomor pendaftaran berikut dan tunggu konfirmasi dari panitia PPDB.
          </p>
        </div>

        {{-- Nomor Pendaftaran --}}
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 rounded-2xl p-6 text-center text-white mb-8">
          <p class="text-xs font-semibold uppercase tracking-widest opacity-75 mb-2">Nomor Pendaftaran Anda</p>
          <div class="flex items-center justify-center gap-3 my-1">
            <p class="text-4xl font-extrabold tracking-widest font-mono" id="kode-pendaftaran">{{ $pendaftaran->kode_regis }}</p>
            <button onclick="salinKode('kode-pendaftaran', this)"
                    title="Salin kode"
                    class="flex-shrink-0 w-9 h-9 bg-white/20 hover:bg-white/35 active:bg-white/50 rounded-xl flex items-center justify-center transition-all group">
              {{-- icon copy --}}
              <svg id="icon-copy-kode-pendaftaran" class="w-4 h-4 text-white group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
              </svg>
              {{-- icon check (hidden) --}}
              <svg id="icon-check-kode-pendaftaran" class="w-4 h-4 text-green-300 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
              </svg>
            </button>
          </div>
          <p class="text-xs opacity-70 mt-1">Ketuk ikon salin untuk menyimpan nomor ini</p>
        </div>

        {{-- Info Pendaftaran --}}
        <div class="bg-gray-50 rounded-2xl p-5 mb-6">
          <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Ringkasan Pendaftaran
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @php
              $rows = [
                ['label' => 'Nama Siswa',      'value' => $pendaftaran->siswa->nama_siswa ?? '-'],
                ['label' => 'NISN',             'value' => $pendaftaran->siswa->nisn ?? '-'],
                ['label' => 'Sekolah Tujuan',   'value' => $pendaftaran->sekolah->nama_sekolah ?? '-'],
                ['label' => 'Jurusan',          'value' => $pendaftaran->jurusan->nama_jurusan ?? 'Tidak Ada (SMP)'],
                ['label' => 'Jalur',            'value' => ucfirst($pendaftaran->jalur_pendaftaran)],
                ['label' => 'Tanggal Daftar',   'value' => \Carbon\Carbon::parse($pendaftaran->tanggal_submit)->translatedFormat('d F Y')],
              ];
            @endphp
            @foreach($rows as $row)
            <div class="flex flex-col gap-0.5 p-3 bg-white rounded-xl border border-gray-100">
              <span class="text-xs text-gray-400 font-medium">{{ $row['label'] }}</span>
              <span class="text-sm font-semibold text-gray-900">{{ $row['value'] }}</span>
            </div>
            @endforeach
          </div>
        </div>

        {{-- Status Badge --}}
        <div class="flex items-center gap-3 bg-yellow-50 border border-yellow-200 rounded-2xl px-5 py-4 mb-6">
          <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <div>
            <p class="text-sm font-bold text-yellow-800">Status: Menunggu Verifikasi</p>
            <p class="text-xs text-yellow-600 mt-0.5">Panitia PPDB akan memverifikasi data dan dokumen Anda dalam beberapa hari kerja.</p>
          </div>
        </div>

        {{-- Langkah Selanjutnya --}}
        <div class="mb-8">
          <h3 class="text-sm font-bold text-gray-700 mb-4">Langkah Selanjutnya</h3>
          <div class="space-y-3">
            @foreach([
              ['num'=>'1','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','title'=>'Verifikasi Dokumen','desc'=>'Panitia akan memeriksa kelengkapan dan keabsahan dokumen Anda.','color'=>'bg-blue-50 text-blue-600'],
              ['num'=>'2','icon'=>'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9','title'=>'Pantau Status','desc'=>'Gunakan nomor pendaftaran untuk cek status secara berkala di halaman Status.','color'=>'bg-orange-50 text-orange-600'],
              ['num'=>'3','icon'=>'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z','title'=>'Pengumuman Hasil','desc'=>'Hasil seleksi akan diumumkan sesuai jadwal PPDB 2026/2027.','color'=>'bg-green-50 text-green-600'],
              ['num'=>'4','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4','title'=>'Daftar Ulang / Pembayaran','desc'=>'Jika diterima, segera lakukan daftar ulang sesuai ketentuan yang berlaku.','color'=>'bg-primary-50 text-primary-600'],
            ] as $step)
            <div class="flex items-start gap-3 p-3 rounded-xl bg-white border border-gray-100">
              <div class="w-8 h-8 {{ $step['color'] }} rounded-lg flex items-center justify-center shrink-0 font-bold text-xs">
                {{ $step['num'] }}
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-900">{{ $step['title'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $step['desc'] }}</p>
              </div>
            </div>
            @endforeach
          </div>
        </div>

        {{-- Notifikasi Email --}}
        <div class="flex items-center gap-3 bg-primary-50 border border-primary-100 rounded-2xl px-5 py-4 mb-8">
          <svg class="w-5 h-5 text-primary-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          <p class="text-xs text-primary-700">Email konfirmasi telah dikirim ke alamat email siswa dan wali yang terdaftar.</p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-3">
          <a href="{{ route('status.index') }}?kode={{ $pendaftaran->kode_regis }}"
             class="flex-1 flex items-center justify-center gap-2 bg-gradient-to-r from-primary-500 to-primary-700 text-white font-bold py-3.5 rounded-2xl hover:shadow-lg hover:scale-105 transition-all text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Cek Status Pendaftaran
          </a>
          <a href="{{ route('home') }}"
             class="flex-1 flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold py-3.5 rounded-2xl hover:bg-gray-200 transition-all text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Kembali ke Beranda
          </a>
        </div>

      </div>{{-- end body --}}

      {{-- Bottom info --}}
      <div class="bg-gray-50 border-t border-gray-100 px-8 py-4 text-center">
        <p class="text-xs text-gray-400">Ada pertanyaan? Hubungi panitia PPDB di <span class="text-primary-600 font-medium">info@fatahillah.sch.id</span></p>
      </div>

    </div>{{-- end card --}}

  </div>
</section>

@endsection