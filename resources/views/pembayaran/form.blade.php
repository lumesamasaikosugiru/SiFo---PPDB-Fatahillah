@extends('layouts.app')
@section('title', 'Form Pembayaran - PPDB Yayasan Fatahillah')

@section('content')

@php
  $bisaBayar      = in_array($pendaftaran->status, ['diterima', 'menunggu_pembayaran']);
  $metodeMidtrans = $metodePembayaran->where('nama_metode', 'otomatis')->first();
  $rekeningList   = $metodePembayaran->where('nama_metode', 'transfer')->values();
  $cashList       = $metodePembayaran->where('nama_metode', 'cash')->values();
  $adaMidtrans    = $metodeMidtrans && !$isLocalhost;
  $waMsg = urlencode("Halo Admin PPDB, saya ingin konfirmasi pembayaran.\nKode: {$pendaftaran->kode_regis}\nNama: " . ($pendaftaran->siswa->nama_siswa ?? '-'));
  $waUrl = "https://wa.me/{$waAdmin}?text={$waMsg}";
@endphp

{{-- Hero --}}
<section class="bg-gradient-to-br from-primary-600 to-primary-800 pt-32 pb-20 px-6 text-white text-center">
  <div class="max-w-3xl mx-auto">
    <span class="inline-block bg-white/10 border border-white/20 text-sm font-medium px-4 py-1.5 rounded-full mb-4">💳 Pembayaran PPDB</span>
    <h1 class="text-3xl md:text-4xl font-extrabold mb-3">Form Pembayaran</h1>
    <p class="text-white/80 font-mono text-lg tracking-widest">{{ $pendaftaran->kode_regis }}</p>
  </div>
</section>

<section class="max-w-2xl mx-auto px-6 -mt-10 relative z-10 mb-20 space-y-5">

  {{-- ========================= --}}
  {{-- STATUS TIDAK BISA BAYAR  --}}
  {{-- ========================= --}}
  @if(!$bisaBayar)
  @php
    $infoMap = [
      'diproses'            => ['bg'=>'bg-yellow-50 border-yellow-200','text'=>'text-yellow-800','ic'=>'text-yellow-500','icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','label'=>'Sedang Diproses','desc'=>'Pendaftaran Anda sedang diverifikasi panitia. Pembayaran hanya bisa dilakukan setelah status menjadi Diterima.'],
      'diverifikasi'        => ['bg'=>'bg-blue-50 border-blue-200','text'=>'text-blue-800','ic'=>'text-blue-500','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4','label'=>'Sedang Diverifikasi','desc'=>'Berkas Anda sedang dalam proses seleksi. Tunggu pengumuman hasil seleksi.'],
      'ditolak'             => ['bg'=>'bg-red-50 border-red-200','text'=>'text-red-800','ic'=>'text-red-500','icon'=>'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z','label'=>'Tidak Diterima','desc'=>'Mohon maaf, pendaftaran Anda tidak lolos seleksi PPDB tahun ini.'],
      'pembayaran_diproses' => ['bg'=>'bg-purple-50 border-purple-200','text'=>'text-purple-800','ic'=>'text-purple-500','icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','label'=>'Pembayaran Sedang Diproses','desc'=>'Bukti bayar Anda sudah diterima dan sedang menunggu verifikasi admin.'],
      'pembayaran_lunas'    => ['bg'=>'bg-teal-50 border-teal-200','text'=>'text-teal-800','ic'=>'text-teal-500','icon'=>'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z','label'=>'Pembayaran Lunas ✓','desc'=>'Pembayaran telah dikonfirmasi. Datang ke sekolah untuk melanjutkan proses pendaftaran.'],
      'selesai'             => ['bg'=>'bg-teal-50 border-teal-200','text'=>'text-teal-800','ic'=>'text-teal-500','icon'=>'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z','label'=>'Pendaftaran Selesai 🎉','desc'=>'Pendaftaran selesai. Selamat bergabung sebagai siswa baru!'],
    ];
    $sInfo = $infoMap[$pendaftaran->status] ?? $infoMap['diproses'];
  @endphp
  <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
    <div class="{{ $sInfo['bg'] }} border px-6 py-6 flex items-start gap-4">
      <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm shrink-0 mt-0.5">
        <svg class="w-6 h-6 {{ $sInfo['ic'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $sInfo['icon'] }}"/></svg>
      </div>
      <div class="flex-1">
        <p class="font-bold {{ $sInfo['text'] }} text-base mb-1">{{ $sInfo['label'] }}</p>
        <p class="text-sm {{ $sInfo['text'] }} opacity-80 leading-relaxed">{{ $sInfo['desc'] }}</p>
        <div class="flex flex-wrap gap-3 mt-4">
          <a href="{{ route('status.index') }}?kode={{ $pendaftaran->kode_regis }}"
             class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors text-sm">
            <span class="text-white">Cek Status Pendaftaran</span>
          </a>
          @if(in_array($pendaftaran->status, ['pembayaran_diproses', 'pembayaran_lunas', 'selesai']))
          <a href="{{ route('pembayaran.status', ['kode' => $pendaftaran->kode_regis]) }}"
             class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors text-sm">
            <span class="text-white">Cek Status Pembayaran</span>
          </a>
          @endif
        </div>
      </div>
    </div>
  </div>

  @else
  {{-- ============================ --}}
  {{-- BISA BAYAR                  --}}
  {{-- ============================ --}}

  {{-- Peringatan Localhost --}}
  @if($isLocalhost)
  <div class="bg-orange-50 border border-orange-300 rounded-2xl px-6 py-5 flex items-start gap-4">
    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
      <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    </div>
    <div class="flex-1">
      <p class="text-sm font-bold text-orange-800">Mode Localhost — Midtrans Tidak Tersedia</p>
      <p class="text-xs text-orange-700 mt-1">Sedang akses dari <strong>localhost</strong>. Midtrans butuh URL publik untuk callback. Gunakan Transfer Bank atau Tunai untuk testing, atau set <code>APP_URL</code> ke domain online.</p>
    </div>
  </div>
  @endif

  {{-- Alert: ada pending Midtrans --}}
  @if($pembayaranPending && $pembayaranPending->snap_token)
  <div class="bg-indigo-50 border border-indigo-200 rounded-2xl px-6 py-5 flex items-start gap-4">
    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
      <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="flex-1">
      <p class="text-sm font-bold text-indigo-900">Ada Sesi Pembayaran Midtrans yang Belum Selesai</p>
      <p class="text-xs text-indigo-700 mt-1 leading-relaxed">Kamu sudah memulai pembayaran Midtrans tapi belum menyelesaikan pilihan metode bayar. Lanjutkan atau batal dan mulai ulang.</p>
      <div class="flex flex-wrap gap-2 mt-3">
        <a href="{{ route('pembayaran.snapLanjut', ['kode' => $pendaftaran->kode_regis]) }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-lg transition-colors">
          ▶ Lanjutkan Pembayaran
        </a>
        {{-- FORM POST wajib — route resetSnap hanya terima POST --}}
        <form method="POST" action="{{ route('pembayaran.resetSnap', ['kode' => $pendaftaran->kode_regis]) }}" id="form-reset-snap-cek">
          @csrf
          <input type="hidden" name="kode" value="{{ $pendaftaran->kode_regis }}">
          <button type="button" id="btn-reset-snap-cek"
                  class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 px-4 py-2 rounded-lg transition-colors border border-red-200">
            ✕ Reset & Pilih Ulang
          </button>
        </form>
      </div>
    </div>
  </div>
  @endif

  {{-- Alert: pembayaran manual sudah disubmit --}}
  @if($pembayaranAktif)
  <div class="bg-yellow-50 border border-yellow-200 rounded-2xl px-6 py-5 flex items-start gap-4">
    <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
      <svg class="w-5 h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    </div>
    <div class="flex-1">
      <p class="text-sm font-bold text-yellow-800">Pembayaran Sudah Disubmit</p>
      <p class="text-xs text-yellow-700 mt-1">Pembayaran sedang menunggu verifikasi admin. Tidak perlu submit ulang.</p>
      <a href="{{ route('pembayaran.status', ['kode' => $pendaftaran->kode_regis]) }}"
         class="inline-flex items-center gap-1.5 mt-3 text-xs font-semibold text-white bg-yellow-500 hover:bg-yellow-600 px-3 py-1.5 rounded-lg transition-colors">
        Cek Status Pembayaran →
      </a>
    </div>
  </div>
  @endif

  {{-- Errors --}}
  @if($errors->any())
  <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl">
    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div>@foreach($errors->all() as $e)<p class="text-sm font-medium">{{ $e }}</p>@endforeach</div>
  </div>
  @endif

  {{-- Nominal Banner --}}
  <div class="bg-gradient-to-r from-primary-500 to-teal-500 rounded-3xl px-7 py-6 flex items-center justify-between shadow-lg">
    <div>
      <p class="text-white/75 text-xs font-semibold uppercase tracking-widest mb-1">Total Tagihan</p>
      <p class="text-white text-5xl font-extrabold tracking-tight leading-none">Rp 200.000</p>
      <p class="text-white/70 text-xs mt-2">Uang pendaftaran PPDB 2026/2027 — dibayar sekali</p>
    </div>
    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center shrink-0">
      <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
      </svg>
    </div>
  </div>

  {{-- Info Pendaftaran --}}
  <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6">
    <h3 class="text-sm font-bold text-gray-800 mb-4">Informasi Pendaftaran</h3>
    <div class="grid grid-cols-2 gap-3">
      @foreach([
        ['Nomor Daftar', $pendaftaran->kode_regis, true],
        ['Nama Siswa', $pendaftaran->siswa->nama_siswa ?? '-', false],
        ['Sekolah', $pendaftaran->sekolah->nama_sekolah ?? '-', false],
        ['Jurusan', $pendaftaran->jurusan->nama_jurusan ?? 'Tidak Ada (SMP)', false],
      ] as [$lbl, $val, $mono])
      <div class="flex flex-col gap-0.5 p-3 bg-gray-50 rounded-xl">
        <span class="text-xs text-gray-400 font-medium">{{ $lbl }}</span>
        <span class="text-sm font-bold text-gray-900 {{ $mono ? 'font-mono tracking-wider' : '' }}">{{ $val }}</span>
      </div>
      @endforeach
    </div>
  </div>

  {{-- ===== FORM PEMBAYARAN (hanya tampil jika belum ada pembayaran aktif/pending) ===== --}}
  @if(!$pembayaranAktif && !$pembayaranPending)
  <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100">
      <h3 class="font-bold text-gray-900">Pilih Metode Pembayaran</h3>
      <p class="text-xs text-gray-500 mt-1">Pilih cara pembayaran yang paling mudah untuk Anda</p>
    </div>

    {{-- MIDTRANS ONLINE --}}
    @if($adaMidtrans)
    <div class="p-6 border-b border-gray-100">
      <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">⚡ Bayar Otomatis (Midtrans)</p>
      <div class="border-2 border-indigo-200 rounded-2xl overflow-hidden bg-indigo-50">
        <div class="px-5 py-4">
          <div class="flex items-start gap-3 mb-3">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <div>
              <div class="flex items-center gap-2 mb-1">
                <span class="font-bold text-indigo-900 text-sm">Pembayaran Online via Midtrans</span>
                <span class="text-xs bg-indigo-600 text-white px-2 py-0.5 rounded-full font-medium">INSTANT</span>
              </div>
              <p class="text-xs text-indigo-700 leading-relaxed">Transfer Bank, QRIS, GoPay, ShopeePay, OVO, DANA, Kartu Kredit, Minimarket. Dikonfirmasi <strong>otomatis</strong>.</p>
            </div>
          </div>
          <button type="button" id="btn-bayar-midtrans"
                  class="w-full bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold py-3.5 rounded-xl transition-all flex items-center justify-center gap-2 text-sm hover:shadow-lg">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            Bayar Sekarang via Midtrans
          </button>
          <p class="text-xs text-indigo-500 text-center mt-2">🔒 Aman & terenkripsi · Dikonfirmasi otomatis</p>
        </div>
      </div>
    </div>
    <div class="px-6 py-3 bg-gray-50 border-b border-gray-100 text-center">
      <span class="text-xs text-gray-400 font-medium">— atau bayar manual —</span>
    </div>
    @endif

    {{-- FORM MANUAL --}}
    <form method="POST" action="{{ route('pembayaran.store') }}" enctype="multipart/form-data" id="form-bayar" novalidate>
      @csrf
      <input type="hidden" name="kode_regis" value="{{ $pendaftaran->kode_regis }}">

      <div class="p-6 space-y-4">

        {{-- Transfer Bank --}}
        @if($rekeningList->count() > 0)
        <div>
          <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">🏦 Transfer Bank</p>
          <div class="border-2 border-gray-200 rounded-2xl overflow-hidden" id="block-transfer">
            <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
              <span class="text-xs font-semibold text-gray-600">Pilih salah satu rekening tujuan</span>
              <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-medium">Bukti wajib</span>
            </div>
            <div class="divide-y divide-gray-100">
              @foreach($rekeningList as $metode)
              @php
                $bagian   = explode(' - ', $metode->deskripsi, 2);
                $bankName = count($bagian) > 1 ? trim(str_replace('Transfer', '', $bagian[0])) : $metode->deskripsi;
                $noRek    = count($bagian) > 1 ? trim($bagian[1]) : '-';
              @endphp
              <label class="flex items-center gap-4 px-4 py-4 cursor-pointer hover:bg-blue-50 transition-colors has-[:checked]:bg-blue-50">
                <input type="radio" name="metode_pembayaran_id" value="{{ $metode->id }}"
                       class="accent-blue-500 w-4 h-4 shrink-0" onchange="onMetodeChange('transfer')">
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-bold text-gray-900 text-sm">{{ trim($bankName) }}</span>
                    <span class="font-mono text-sm text-gray-700 bg-gray-100 px-2 py-0.5 rounded-lg">{{ $noRek }}</span>
                  </div>
                  <p class="text-xs text-gray-400 mt-0.5">a.n. Yayasan Fatahillah</p>
                </div>
                <button type="button" onclick="copyText('{{ $noRek }}', this)"
                        class="shrink-0 text-xs text-blue-600 font-semibold bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">Salin</button>
              </label>
              @endforeach
            </div>
            <div class="px-4 py-3 bg-blue-50 border-t border-blue-100 flex items-center justify-between">
              <span class="text-xs text-blue-700 font-medium">⚠️ Transfer tepat sejumlah:</span>
              <span class="text-base font-extrabold text-blue-800">Rp 200.000</span>
            </div>
          </div>
        </div>
        @endif

        {{-- Tunai --}}
        @if($cashList->count() > 0)
        <div>
          <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">💵 Tunai (Cash)</p>
          @foreach($cashList as $metode)
          <label class="flex items-start gap-4 p-4 border-2 border-gray-200 rounded-2xl cursor-pointer hover:border-green-300 hover:bg-green-50 transition-all has-[:checked]:border-green-400 has-[:checked]:bg-green-50">
            <input type="radio" name="metode_pembayaran_id" value="{{ $metode->id }}"
                   class="mt-0.5 accent-green-500 w-4 h-4 shrink-0" onchange="onMetodeChange('cash')">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                <span class="font-bold text-gray-900 text-sm">Pembayaran Tunai</span>
                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Bukti opsional</span>
              </div>
              <p class="text-xs text-gray-500">{{ $metode->deskripsi }}</p>
            </div>
          </label>
          @endforeach
        </div>
        @endif

        {{-- Info Transfer --}}
        <div id="info-transfer" class="hidden bg-blue-50 border border-blue-200 rounded-2xl p-4">
          <p class="text-xs font-bold text-blue-800 mb-2">📋 Petunjuk Transfer</p>
          <ol class="text-xs text-blue-700 space-y-1.5 list-decimal list-inside">
            <li>Pilih salah satu rekening bank di atas</li>
            <li>Transfer <strong>tepat Rp 200.000</strong> ke rekening tersebut</li>
            <li>Simpan bukti transfer (screenshot / struk ATM)</li>
            <li>Upload bukti di bawah, lalu klik <strong>Submit Pembayaran</strong></li>
          </ol>
        </div>

        {{-- Info Cash --}}
        <div id="info-cash" class="hidden bg-green-50 border border-green-200 rounded-2xl p-4">
          <p class="text-xs font-bold text-green-800 mb-2">📍 Cara Bayar Tunai</p>
          <p class="text-xs text-green-700">Datang ke <strong>{{ $pendaftaran->sekolah->nama_sekolah ?? 'sekolah tujuan' }}</strong> dengan membawa nomor <strong>{{ $pendaftaran->kode_regis }}</strong> dan uang tunai <strong>Rp 200.000</strong>.</p>
        </div>

        {{-- Upload Bukti --}}
        <div id="wrapper-proof" class="hidden">
          <label class="block text-sm font-semibold text-gray-700 mb-2">
            Bukti Pembayaran
            <span id="proof-required-badge" class="ml-1 text-xs bg-red-100 text-red-600 px-1.5 py-0.5 rounded-full font-medium hidden">Wajib</span>
            <span id="proof-optional-badge" class="ml-1 text-xs bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded-full font-medium hidden">Opsional</span>
          </label>
          <div id="proof-dropzone"
               class="border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center hover:border-primary-300 hover:bg-primary-50 transition-all cursor-pointer min-h-[120px] flex items-center justify-center"
               onclick="document.getElementById('proof-input').click()">
            <div class="flex flex-col items-center gap-2">
              <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
              <p class="text-sm font-medium text-gray-600">Klik untuk upload bukti transfer</p>
              <p class="text-xs text-gray-400">JPG, PNG, PDF — maks. 5MB</p>
            </div>
          </div>
          <p id="proof-filename" class="text-xs text-primary-600 font-semibold mt-2 hidden"></p>
          <input type="file" id="proof-input" name="proof" accept=".jpg,.jpeg,.png,.pdf" class="hidden" onchange="onProofChange(this)">
          @error('proof')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Submit --}}
        <div class="pt-2">
          <button type="button" id="btn-submit-trigger"
                  class="w-full bg-gradient-to-r from-primary-400 to-primary-600 text-white font-bold py-4 rounded-2xl hover:shadow-xl hover:scale-[1.02] transition-all text-sm flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Submit Pembayaran Manual
          </button>
          {{-- Hidden real submit --}}
          <button type="submit" id="btn-submit-real" class="hidden"></button>
          <p class="text-xs text-gray-400 text-center mt-2">Pembayaran diverifikasi admin dalam 1×24 jam kerja.</p>
        </div>
      </div>
    </form>
  </div>
  @endif

  @endif {{-- end @else bisaBayar --}}

  {{-- Hubungi Admin --}}
  <div class="bg-white rounded-2xl border border-gray-100 shadow px-6 py-5 flex items-center justify-between gap-4">
    <div>
      <p class="text-sm font-bold text-gray-800">Butuh Bantuan?</p>
      <p class="text-xs text-gray-500 mt-0.5">Hubungi admin PPDB via WhatsApp.</p>
    </div>
    <a href="{{ $waUrl }}" target="_blank"
       class="shrink-0 flex items-center gap-2 bg-[#25D366] hover:bg-[#1ebe5d] text-white font-semibold px-4 py-2.5 rounded-xl transition-colors text-sm">
      <svg class="w-4 h-4 text-white shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
      <span class="text-white">Chat Admin WA</span>
    </a>
  </div>

</section>

@if($bisaBayar && $adaMidtrans)
<script src="{{ config('midtrans.snap_js_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endif

@push('scripts')
<script>
// =====================================================
// MIDTRANS HANDLER
// =====================================================
@if($bisaBayar && $adaMidtrans)
document.getElementById('btn-bayar-midtrans')?.addEventListener('click', async function() {
  const btn = this;
  btn.disabled = true;
  btn.innerHTML = `<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Menyiapkan pembayaran...`;

  try {
    const resp = await fetch('{{ route("pembayaran.requestSnapToken") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ kode_regis: '{{ $pendaftaran->kode_regis }}' })
    });
    const data = await resp.json();

    if (!data.status) {
      Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal memulai pembayaran.' });
      resetMidtransBtn(btn);
      return;
    }

    window.snap.pay(data.snap_token, {
      onSuccess: function(result) {
        btn.disabled = true;
        btn.innerHTML = `<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Memproses...`;
        fetch('{{ route("pembayaran.paymentSuccess") }}', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          body: JSON.stringify({
            snap_token:   data.snap_token,
            order_id:     result.order_id ?? data.order_id ?? '',
            payment_type: result.payment_type ?? 'midtrans',
          }),
        })
        .then(r => r.json())
        .then(res => { window.location.href = res.redirect_to ?? '{{ route("pembayaran.sukses", ["kode" => $pendaftaran->kode_regis]) }}'; })
        .catch(()  => { window.location.href = '{{ route("pembayaran.sukses", ["kode" => $pendaftaran->kode_regis]) }}'; });
      },
      onPending: function() {
        window.location.href = '{{ route("pembayaran.snapLanjut", ["kode" => $pendaftaran->kode_regis]) }}';
      },
      onError: function() {
        Swal.fire({ icon: 'error', title: 'Pembayaran Gagal', text: 'Terjadi kesalahan. Silakan coba lagi.' });
        resetMidtransBtn(btn);
      },
      onClose: function() {
        resetMidtransBtn(btn);
      }
    });
  } catch(e) {
    console.error(e);
    Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi bermasalah. Periksa internet dan coba lagi.' });
    resetMidtransBtn(btn);
  }
});

function resetMidtransBtn(btn) {
  btn.disabled = false;
  btn.innerHTML = `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg> Bayar Sekarang via Midtrans`;
}
@endif

// =====================================================
// RESET SNAP (tombol di alert pending midtrans)
// =====================================================
document.getElementById('btn-reset-snap-cek')?.addEventListener('click', function() {
  Swal.fire({
    icon: 'question',
    title: 'Reset sesi Midtrans?',
    html: `<p class="text-sm text-gray-600">Sesi pembayaran Midtrans akan dibatalkan.<br>Kamu bisa pilih metode lain (Transfer, Tunai, atau Midtrans baru).</p>`,
    showCancelButton: true,
    confirmButtonText: '✕ Ya, Reset & Pilih Ulang',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280',
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('form-reset-snap-cek').submit();
    }
  });
});

// =====================================================
// MANUAL FORM HANDLERS
// =====================================================

// State tracker: metode yang sedang dipilih
let selectedMetode = null;

function onMetodeChange(nama) {
  selectedMetode = nama;

  // Reset semua info box
  document.getElementById('info-transfer').classList.add('hidden');
  document.getElementById('info-cash').classList.add('hidden');
  document.getElementById('wrapper-proof').classList.add('hidden');

  const proofInput = document.getElementById('proof-input');
  const reqBadge   = document.getElementById('proof-required-badge');
  const optBadge   = document.getElementById('proof-optional-badge');

  if (nama === 'transfer') {
    document.getElementById('info-transfer').classList.remove('hidden');
    document.getElementById('wrapper-proof').classList.remove('hidden');
    proofInput.required = true;
    reqBadge?.classList.remove('hidden');
    optBadge?.classList.add('hidden');

    // Reset file jika ada
    proofInput.value = '';
    const fname = document.getElementById('proof-filename');
    if (fname) { fname.textContent = ''; fname.classList.add('hidden'); }

    // Reset dropzone
    const dz = document.getElementById('proof-dropzone');
    if (dz) {
      dz.innerHTML = `
        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        <p class="text-sm font-medium text-gray-600 mb-1">Klik untuk upload bukti transfer</p>
        <p class="text-xs text-gray-400">JPG, PNG, PDF — maks. 5MB</p>`;
    }

  } else if (nama === 'cash') {
    document.getElementById('info-cash').classList.remove('hidden');
    document.getElementById('wrapper-proof').classList.remove('hidden');
    proofInput.required = false;
    reqBadge?.classList.add('hidden');
    optBadge?.classList.remove('hidden');
  }
}

function copyText(text, btn) {
  navigator.clipboard.writeText(text).then(() => {
    const orig = btn.textContent;
    btn.textContent = 'Disalin ✓';
    btn.classList.add('text-green-600');
    setTimeout(() => { btn.textContent = orig; btn.classList.remove('text-green-600'); }, 2000);
  });
}

function onProofChange(input) {
  const file = input.files[0];
  if (!file) return;

  // Validasi ukuran
  if (file.size > 5 * 1024 * 1024) {
    Swal.fire({
      icon: 'error',
      title: 'File Terlalu Besar',
      text: 'Ukuran file maksimal 5MB. Silakan kompres atau pilih file lain.',
      confirmButtonColor: '#0f4c3a',
    });
    input.value = '';
    return;
  }

  const dz   = document.getElementById('proof-dropzone');
  const fname = document.getElementById('proof-filename');
  const isImage = file.type.startsWith('image/');
  const fileSizeKB = (file.size / 1024).toFixed(0);

  if (isImage) {
    // ── Preview gambar nyata via FileReader ──
    const reader = new FileReader();
    reader.onload = function(e) {
      if (dz) {
        dz.innerHTML = `
          <div class="relative w-full">
            <img src="${e.target.result}"
                 alt="Preview bukti transfer"
                 class="w-full max-h-56 object-contain rounded-xl border border-green-200 mb-3"
                 style="background:#f0fdf4">
            <div class="flex items-center justify-center gap-2 flex-wrap">
              <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-100 px-3 py-1.5 rounded-full">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                ${file.name}
              </span>
              <span class="text-xs text-gray-400">${fileSizeKB} KB</span>
            </div>
            <p class="text-xs text-green-600 mt-2">✓ Gambar berhasil dipilih · Klik untuk ganti</p>
          </div>`;
      }

      // Toast sukses
      Swal.fire({
        icon: 'success',
        title: 'Bukti Transfer Dipilih!',
        text: `${file.name} (${fileSizeKB} KB) siap diupload.`,
        timer: 2000,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
      });
    };
    reader.readAsDataURL(file);

  } else {
    // ── PDF atau file lain — icon saja ──
    if (dz) {
      dz.innerHTML = `
        <div class="flex flex-col items-center gap-2">
          <div class="w-14 h-14 bg-red-50 border-2 border-red-200 rounded-2xl flex items-center justify-center">
            <svg class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-800">${file.name}</p>
            <p class="text-xs text-gray-400">${fileSizeKB} KB · PDF</p>
          </div>
          <p class="text-xs text-green-600 font-medium">✓ File dipilih · Klik untuk ganti</p>
        </div>`;
    }

    Swal.fire({
      icon: 'success',
      title: 'File Dipilih!',
      text: `${file.name} (${fileSizeKB} KB) siap diupload.`,
      timer: 2000,
      timerProgressBar: true,
      showConfirmButton: false,
      toast: true,
      position: 'top-end',
    });
  }

  if (fname) { fname.textContent = '✓ ' + file.name; fname.classList.remove('hidden'); }
}

// =====================================================
// SUBMIT BUTTON — validasi dengan SweetAlert
// =====================================================
document.getElementById('btn-submit-trigger')?.addEventListener('click', function() {
  const form       = document.getElementById('form-bayar');
  const proofInput = document.getElementById('proof-input');

  // Cek metode dipilih
  const metodeChecked = form ? form.querySelector('input[name="metode_pembayaran_id"]:checked') : null;
  if (!metodeChecked) {
    Swal.fire({
      icon: 'warning',
      title: 'Pilih Metode Pembayaran',
      text: 'Silakan pilih metode pembayaran terlebih dahulu.',
      confirmButtonColor: '#0f4c3a',
    });
    return;
  }

  // Cek bukti transfer
  const isTransfer = (selectedMetode === 'transfer') || (proofInput && proofInput.required);
  if (isTransfer && (!proofInput || !proofInput.files || proofInput.files.length === 0)) {
    Swal.fire({
      icon: 'warning',
      title: '📸 Upload Bukti Transfer Dulu!',
      text: 'Pembayaran transfer wajib menyertakan foto/screenshot bukti transfer.',
      confirmButtonText: '📂 Upload Sekarang',
      confirmButtonColor: '#0f4c3a',
      showCancelButton: true,
      cancelButtonText: 'Nanti',
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('proof-input')?.click();
      }
    });
    return;
  }

  // Semua valid — trigger real submit
  const realBtn = document.getElementById('btn-submit-real');
  if (realBtn) {
    // Ubah tampilan trigger button
    document.getElementById('btn-submit-trigger').disabled = true;
    document.getElementById('btn-submit-trigger').innerHTML = `<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Memproses...`;
    realBtn.click();
  }
});
</script>
@endpush

@endsection