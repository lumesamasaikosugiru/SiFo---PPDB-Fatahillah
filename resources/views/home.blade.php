@extends('layouts.app')
@section('title', 'Beranda - PPDB Yayasan Fatahillah 2026/2027')

@section('content')

{{-- ===== HERO SECTION ===== --}}
<section class="relative min-h-screen flex items-center overflow-hidden gradient-hero">
  <div class="absolute top-20 right-10 w-72 h-72 bg-white/5 rounded-full blur-3xl"></div>
  <div class="absolute bottom-20 left-10 w-96 h-96 bg-primary-300/10 rounded-full blur-3xl"></div>
  <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-white/3 rounded-full blur-3xl"></div>

  <div class="relative max-w-7xl mx-auto px-6 py-32 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
    {{-- Left Content --}}
    <div class="text-white">
      <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-2 text-sm font-medium mb-6">
        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
        Pendaftaran PPDB 2026/2027 Dibuka
      </div>
      <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
        Wujudkan Masa <br/>
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-300 to-teal-200">
          Depan Cerahmu
        </span>
        <br/> Bersama Kami
      </h1>
      <p class="text-lg text-white/80 leading-relaxed mb-8 max-w-lg">
        Daftarkan diri Anda di sekolah terbaik Yayasan Fatahillah. Proses mudah, cepat, dan transparan secara online.
      </p>
      <div class="flex flex-wrap gap-4">
        <a href="{{ route('daftar.create') }}"
           class="inline-flex items-center gap-2 bg-white text-primary-700 font-bold px-8 py-4 rounded-2xl shadow-xl hover:shadow-2xl hover:scale-105 transition-all text-base">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
          Daftar Sekarang
        </a>
        <a href="{{ route('status.index') }}"
           class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/30 text-white font-semibold px-8 py-4 rounded-2xl hover:bg-white/20 transition-all text-base">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
          Cek Status
        </a>
      </div>

      {{-- Stats --}}
      <div class="flex flex-wrap gap-8 mt-12 pt-8 border-t border-white/20">
        <div>
          <div class="text-3xl font-extrabold text-white">4+</div>
          <div class="text-sm text-white/60 mt-1">Sekolah Unggulan</div>
        </div>
        <div>
          <div class="text-3xl font-extrabold text-white">1000+</div>
          <div class="text-sm text-white/60 mt-1">Siswa Terdaftar</div>
        </div>
        <div>
          <div class="text-3xl font-extrabold text-white">{{ $jumlahJurusan ?? '15' }}+</div>
          <div class="text-sm text-white/60 mt-1">Program Jurusan</div>
        </div>
        <div>
          <div class="text-3xl font-extrabold text-white">20+</div>
          <div class="text-sm text-white/60 mt-1">Tahun Berpengalaman</div>
        </div>
      </div>
    </div>

    {{-- Right Card --}}
    <div class="hidden lg:block">
      <div class="relative">
        <div class="glass-card rounded-3xl p-8 shadow-2xl border border-white/50">
          <h3 class="font-bold text-gray-800 text-lg mb-6 flex items-center gap-2">
            <span class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </span>
            Jadwal PPDB 2026/2027
          </h3>
          <div class="space-y-4">
            @php
              $jadwal = [
                ['label' => 'Pendaftaran Online',  'date' => '1 - 30 Mei 2026',    'status' => 'active'],
                ['label' => 'Verifikasi Berkas',   'date' => '1 - 5 Jun 2026',     'status' => 'upcoming'],
                ['label' => 'Proses Seleksi',      'date' => '8 - 10 Jun 2026',    'status' => 'upcoming'],
                ['label' => 'Pengumuman',           'date' => '15 Jun 2026',        'status' => 'upcoming'],
                ['label' => 'Daftar Ulang',         'date' => '16 - 20 Jun 2026',  'status' => 'upcoming'],
              ];
            @endphp
            @foreach($jadwal as $item)
            <div class="flex items-center gap-4 p-3 rounded-xl {{ $item['status'] === 'active' ? 'bg-primary-50 border border-primary-200' : 'hover:bg-gray-50' }} transition-colors">
              <div class="w-2.5 h-2.5 rounded-full {{ $item['status'] === 'active' ? 'bg-primary-500 animate-pulse' : 'bg-gray-300' }} shrink-0"></div>
              <div class="flex-1">
                <div class="font-semibold text-sm text-gray-800">{{ $item['label'] }}</div>
                <div class="text-xs text-gray-500 mt-0.5">{{ $item['date'] }}</div>
              </div>
              @if($item['status'] === 'active')
              <span class="text-xs bg-primary-500 text-white px-2 py-0.5 rounded-full font-medium">Aktif</span>
              @endif
            </div>
            @endforeach
          </div>
        </div>
        <div class="absolute -top-4 -right-4 glass-card rounded-2xl px-4 py-3 shadow-xl border border-white/50 animate-float">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div>
              <div class="text-xs font-bold text-gray-800">Gratis Biaya</div>
              <div class="text-xs text-gray-500">Pendaftaran Online</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Wave --}}
  <div class="absolute bottom-0 left-0 right-0">
    <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0 80L60 74.7C120 69.3 240 58.7 360 53.3C480 48 600 48 720 53.3C840 58.7 960 69.3 1080 72C1200 74.7 1320 69.3 1380 66.7L1440 64V80H1380C1320 80 1200 80 1080 80C960 80 840 80 720 80C600 80 480 80 360 80C240 80 120 80 60 80H0Z" fill="#f9fafb"/>
    </svg>
  </div>
</section>

{{-- ===== SEKOLAH SECTION ===== --}}
<section id="sekolah" class="bg-gray-50 py-24">
  <div class="max-w-7xl mx-auto px-6">
    <div class="text-center mb-14">
      <span class="inline-block bg-primary-100 text-primary-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-4">Sekolah Kami</span>
      <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Pilih Sekolah Terbaik <br/> untuk Masa Depanmu</h2>
      <p class="text-gray-500 max-w-xl mx-auto">Yayasan Fatahillah mengelola sekolah unggulan dengan program berkualitas dan fasilitas modern.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      @php
        $sekolahCards = [
          [
            'nama'      => 'SMK YP Fatahillah 1 Cilegon',
            'key'       => 'smk1cilegon',
            'logo'      => 'logo-SMK_YP_Fatahillah_1_Cilegon.jpeg',
            'tingkatan' => 'SMK',
            'desc'      => 'Sekolah kejuruan unggulan dengan berbagai program keahlian teknik dan teknologi.',
            'color'     => 'from-blue-500 to-blue-700',
            'bg'        => 'bg-blue-50',
            'text'      => 'text-blue-700',
            'border'    => 'border-blue-200',
            'sekolah_q' => 'SMK YP Fatahillah 1 Cilegon',
          ],
          [
            'nama'      => 'SMK YP Fatahillah 2 Cilegon',
            'key'       => 'smk2cilegon',
            'logo'      => 'logo-SMK_YP_Fatahillah_2_Cilegon.png',
            'tingkatan' => 'SMK',
            'desc'      => 'Program keahlian bisnis, akuntansi, dan administrasi perkantoran terbaik.',
            'color'     => 'from-purple-500 to-purple-700',
            'bg'        => 'bg-purple-50',
            'text'      => 'text-purple-700',
            'border'    => 'border-purple-200',
            'sekolah_q' => 'SMK YP Fatahillah 2 Cilegon',
          ],
          [
            'nama'      => 'SMK YP Fatahillah 1 Kramatwatu',
            'key'       => 'smk1kramatwatu',
            'logo'      => 'logo-SMK_YP_Fatahillah_1_Kramatwatu.jpeg',
            'tingkatan' => 'SMK',
            'desc'      => 'Sekolah kejuruan dengan fokus pada industri dan manufaktur modern.',
            'color'     => 'from-orange-500 to-orange-700',
            'bg'        => 'bg-orange-50',
            'text'      => 'text-orange-700',
            'border'    => 'border-orange-200',
            'sekolah_q' => 'SMK YP Fatahillah 1 Kramatwatu',
          ],
          [
            'nama'      => 'SMP YP Fatahillah Cilegon',
            'key'       => 'smpcilegon',
            'logo'      => 'logo-SMP_Fatahillah_Cilegon.jpeg',
            'tingkatan' => 'SMP',
            'desc'      => 'Pendidikan menengah pertama berkualitas dengan kurikulum nasional dan karakter islami.',
            'color'     => 'from-primary-400 to-primary-600',
            'bg'        => 'bg-primary-50',
            'text'      => 'text-primary-700',
            'border'    => 'border-primary-200',
            'sekolah_q' => 'SMP YP Fatahillah Cilegon',
          ],
        ];
      @endphp

      @foreach($sekolahCards as $s)
      <div class="card-hover bg-white rounded-2xl overflow-hidden shadow-md border border-gray-100 flex flex-col">
        <div class="h-2 bg-gradient-to-r {{ $s['color'] }}"></div>
        <div class="p-6 flex-1">
          {{-- Logo --}}
          <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center">
            <img src="{{ asset('assets/images/' . $s['logo']) }}"
                 alt="Logo {{ $s['nama'] }}"
                 class="w-full h-full object-contain"
                 onerror="this.onerror=null;this.style.display='none';this.parentElement.classList.add('bg-gray-100','rounded-xl')">
          </div>
          <div class="text-center">
            <span class="text-xs font-semibold {{ $s['text'] }} {{ $s['bg'] }} px-2 py-1 rounded-full">{{ $s['tingkatan'] }}</span>
            <h3 class="font-bold text-gray-900 mt-3 mb-2 text-base leading-snug">{{ $s['nama'] }}</h3>
            <p class="text-sm text-gray-500 leading-relaxed">{{ $s['desc'] }}</p>
          </div>
        </div>
        <div class="px-6 pb-6 flex gap-2">
          <a href="{{ route('daftar.create') }}?sekolah={{ urlencode($s['sekolah_q']) }}"
             class="flex-1 block text-center text-sm font-semibold {{ $s['text'] }} border {{ $s['border'] }} rounded-xl py-2 hover:{{ $s['bg'] }} transition-colors">
            Daftar
          </a>
          <button onclick="showDetailSekolah('{{ $s['key'] }}')"
             class="flex-1 block text-center text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl py-2 hover:bg-gray-50 transition-colors">
            Detail
          </button>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ===== MODAL DETAIL SEKOLAH ===== --}}
<div id="modal-sekolah" class="fixed inset-0 z-50 hidden flex items-center justify-center px-4">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModalSekolah()"></div>
  <div class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-full p-8 z-10">
    <button onclick="closeModalSekolah()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
      <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <div id="modal-sekolah-content"></div>
  </div>
</div>

{{-- ===== ALUR PENDAFTARAN SECTION ===== --}}
<section id="alur" class="py-24 bg-white">
  <div class="max-w-7xl mx-auto px-6">
    <div class="text-center mb-14">
      <span class="inline-block bg-primary-100 text-primary-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-4">Alur Pendaftaran</span>
      <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Cara Mudah Mendaftar PPDB</h2>
      <p class="text-gray-500 max-w-xl mx-auto">Ikuti langkah-langkah berikut untuk menyelesaikan proses pendaftaran dengan mudah dan cepat.</p>
    </div>

    <div class="relative">
      <div class="hidden md:block absolute top-8 left-0 right-0 h-0.5 bg-gradient-to-r from-primary-200 via-primary-400 to-primary-200 mx-16"></div>
      <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
        @php
          $alurSteps = [
            ['num' => '01', 'title' => 'Isi Formulir',      'desc' => 'Lengkapi formulir pendaftaran online dengan data diri yang benar dan lengkap.', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'active' => true, 'emoji' => '📝'],
            ['num' => '02', 'title' => 'Pilih Sekolah',     'desc' => 'Pilih sekolah dan jurusan yang sesuai dengan minat dan kemampuan Anda.', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5', 'active' => true, 'emoji' => '🏫'],
            ['num' => '03', 'title' => 'Upload Dokumen',    'desc' => 'Upload dokumen persyaratan seperti ijazah, KK, akta kelahiran, dan pas foto.', 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12', 'active' => false, 'emoji' => '📁'],
            ['num' => '04', 'title' => 'Tunggu Verifikasi', 'desc' => 'Panitia PPDB akan memverifikasi data dan dokumen yang telah Anda submit.', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'active' => false, 'emoji' => '⏳'],
            ['num' => '05', 'title' => 'Pengumuman & Daftar Ulang', 'desc' => 'Cek hasil seleksi dan lakukan daftar ulang jika dinyatakan diterima.', 'icon' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z', 'active' => false, 'emoji' => '🎉'],
          ];
        @endphp
        @foreach($alurSteps as $step)
        <div class="flex flex-col items-center text-center">
          <div class="relative z-10 w-16 h-16 rounded-2xl {{ $step['active'] ? 'bg-gradient-to-br from-primary-400 to-primary-600 shadow-lg shadow-primary-200' : 'bg-gray-100' }} flex items-center justify-center mb-4 transition-all">
            <span class="text-2xl">{{ $step['emoji'] }}</span>
            <span class="absolute -top-2 -right-2 w-6 h-6 {{ $step['active'] ? 'bg-primary-500' : 'bg-gray-300' }} text-white text-xs font-bold rounded-full flex items-center justify-center">
              {{ $step['num'] }}
            </span>
          </div>
          <h3 class="font-bold text-gray-900 mb-2">{{ $step['title'] }}</h3>
          <p class="text-sm text-gray-500 leading-relaxed">{{ $step['desc'] }}</p>
        </div>
        @endforeach
      </div>
    </div>

    <div class="mt-12 text-center">
      <a href="{{ route('daftar.create') }}"
         class="inline-flex items-center gap-2 bg-gradient-to-r from-primary-400 to-primary-600 text-white font-bold px-10 py-4 rounded-2xl shadow-xl hover:shadow-2xl hover:scale-105 transition-all text-base">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Mulai Pendaftaran Sekarang
      </a>
    </div>
  </div>
</section>

{{-- ===== JURUSAN SECTION ===== --}}
<section class="py-24 bg-gray-50">
  <div class="max-w-7xl mx-auto px-6">
    <div class="text-center mb-14">
      <span class="inline-block bg-primary-100 text-primary-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-4">Program Keahlian</span>
      <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Jurusan yang Tersedia</h2>
      <p class="text-gray-500 max-w-xl mx-auto">Pilih program keahlian yang sesuai dengan minat dan bakat Anda untuk masa depan yang lebih cerah.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      @foreach(($sekolahGroup ?? collect([])) as $tingkatan => $sekolahs)
        @foreach($sekolahs as $sekolah)
          @if($sekolah->jurusans && $sekolah->jurusans->count() > 0)
          <div class="bg-white rounded-2xl p-6 border border-gray-100 card-hover">
            <div class="flex items-center gap-3 mb-5">
              <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-gray-900 text-sm leading-tight">{{ $sekolah->nama_sekolah }}</h3>
                <span class="text-xs text-primary-600 font-medium">{{ $sekolah->jurusans->count() }} Jurusan</span>
              </div>
            </div>
            <ul class="space-y-2">
              @foreach($sekolah->jurusans as $jurusan)
              <li class="flex items-center gap-2 text-sm text-gray-600">
                <svg class="w-4 h-4 text-primary-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $jurusan->nama_jurusan }}
              </li>
              @endforeach
            </ul>
          </div>
          @endif
        @endforeach
      @endforeach
    </div>
  </div>
</section>

{{-- ===== PERSYARATAN SECTION ===== --}}
<section class="py-24 bg-white">
  <div class="max-w-7xl mx-auto px-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
      <div>
        <span class="inline-block bg-primary-100 text-primary-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-4">Persyaratan</span>
        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-6">Dokumen yang Perlu <br/> Disiapkan</h2>
        <p class="text-gray-500 mb-8 leading-relaxed">Siapkan dokumen berikut sebelum melakukan pendaftaran agar proses berjalan lancar dan cepat.</p>
        <div class="space-y-4">
          @php
            $docs = [
              ['name' => 'Pas Foto', 'desc' => 'Foto terbaru ukuran 3x4 background merah', 'required' => true],
              ['name' => 'Kartu Keluarga', 'desc' => 'Kartu Keluarga yang masih berlaku', 'required' => true],
              ['name' => 'Akta Kelahiran', 'desc' => 'Akta kelahiran dari Dinas Kependudukan', 'required' => true],
              ['name' => 'Ijazah / SKL', 'desc' => 'Ijazah asli atau Surat Keterangan Lulus', 'required' => false],
              ['name' => 'SKHUN', 'desc' => 'Surat Keterangan Hasil Ujian Nasional', 'required' => false],
              ['name' => 'STL', 'desc' => 'Surat Tanda Lulus dari sekolah asal', 'required' => false],
            ];
          @endphp
          @foreach($docs as $i => $doc)
          <div class="flex items-start gap-4 p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-8 h-8 {{ $doc['required'] ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-500' }} rounded-lg flex items-center justify-center shrink-0 font-bold text-sm">
              {{ $i + 1 }}
            </div>
            <div class="flex-1">
              <div class="flex items-center gap-2">
                <div class="font-semibold text-gray-800 text-sm">{{ $doc['name'] }}</div>
                @if($doc['required'])
                <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-medium">Wajib</span>
                @else
                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full font-medium">Opsional</span>
                @endif
              </div>
              <div class="text-xs text-gray-500 mt-0.5">{{ $doc['desc'] }}</div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
      <div class="relative">
        <div class="bg-gradient-to-br from-primary-400 to-primary-700 rounded-3xl p-8 text-white shadow-2xl">
          <h3 class="text-xl font-bold mb-6">Jalur Pendaftaran</h3>
          <div class="space-y-4">
            @php
              $jalur = [
                ['name' => 'Jalur Regular',  'desc' => 'Pendaftaran umum untuk semua calon siswa baru tanpa syarat khusus.', 'icon' => '📋'],
                ['name' => 'Jalur Prestasi', 'desc' => 'Untuk siswa berprestasi akademik maupun non-akademik dengan bukti sertifikat.', 'icon' => '🏆'],
                ['name' => 'Jalur Afirmasi', 'desc' => 'Untuk siswa dari keluarga kurang mampu dengan bukti surat keterangan.', 'icon' => '🤝'],
                ['name' => 'Jalur Pindahan', 'desc' => 'Untuk siswa yang pindah dari sekolah lain dengan keterangan alasan pindah.', 'icon' => '🔄'],
              ];
            @endphp
            @foreach($jalur as $j)
            <div class="flex items-start gap-4 bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
              <span class="text-2xl">{{ $j['icon'] }}</span>
              <div>
                <div class="font-semibold text-sm">{{ $j['name'] }}</div>
                <div class="text-xs text-white/70 mt-1 leading-relaxed">{{ $j['desc'] }}</div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
        <div class="absolute -bottom-6 -left-6 glass-card rounded-2xl p-4 shadow-xl border border-white/50">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div>
              <div class="font-bold text-gray-800 text-sm">100% Online</div>
              <div class="text-xs text-gray-500">Daftar dari mana saja</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ===== KEUNGGULAN SECTION ===== --}}
<section class="py-24 bg-gray-50">
  <div class="max-w-7xl mx-auto px-6">
    <div class="text-center mb-14">
      <span class="inline-block bg-primary-100 text-primary-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-4">Keunggulan</span>
      <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Mengapa Memilih <br/> Yayasan Fatahillah?</h2>
      <p class="text-gray-500 max-w-xl mx-auto">Kami berkomitmen memberikan pendidikan terbaik dengan fasilitas modern dan tenaga pengajar berpengalaman.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      @php
        $keunggulan = [
          ['icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'title' => 'Kurikulum Berkualitas', 'desc' => 'Kurikulum nasional yang dipadukan dengan pendidikan karakter islami dan keterampilan vokasional.', 'color' => 'bg-blue-50 text-blue-600'],
          ['icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z', 'title' => 'Fasilitas Modern', 'desc' => 'Laboratorium komputer, bengkel praktik, perpustakaan digital, dan fasilitas olahraga lengkap.', 'color' => 'bg-purple-50 text-purple-600'],
          ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'title' => 'Tenaga Pengajar Ahli', 'desc' => 'Guru berpengalaman dan bersertifikat dengan dedikasi tinggi dalam mendidik generasi bangsa.', 'color' => 'bg-green-50 text-green-600'],
          ['icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', 'title' => 'Akreditasi Terbaik', 'desc' => 'Seluruh sekolah telah terakreditasi A oleh BAN-S/M dengan standar pendidikan nasional.', 'color' => 'bg-yellow-50 text-yellow-600'],
          ['icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'title' => 'Peluang Karir Luas', 'desc' => 'Lulusan kami siap kerja dengan keterampilan industri dan jaringan alumni yang kuat.', 'color' => 'bg-red-50 text-red-600'],
          ['icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Lingkungan Islami', 'desc' => 'Suasana belajar yang kondusif dengan nilai-nilai islami, disiplin, dan akhlak mulia.', 'color' => 'bg-teal-50 text-teal-600'],
        ];
      @endphp
      @foreach($keunggulan as $item)
      <div class="card-hover bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="w-12 h-12 {{ $item['color'] }} rounded-xl flex items-center justify-center mb-5">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
          </svg>
        </div>
        <h3 class="font-bold text-gray-900 mb-2">{{ $item['title'] }}</h3>
        <p class="text-sm text-gray-500 leading-relaxed">{{ $item['desc'] }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ===== TESTIMONI SECTION (SWIPER SLIDER) ===== --}}
<section class="py-24 bg-white overflow-hidden">
  <div class="max-w-7xl mx-auto px-6">
    <div class="text-center mb-14">
      <span class="inline-block bg-primary-100 text-primary-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-4">Testimoni</span>
      <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Apa Kata Mereka?</h2>
      <p class="text-gray-500 max-w-xl mx-auto">Dengarkan pengalaman siswa dan orang tua yang telah mempercayakan pendidikan kepada kami.</p>
    </div>

    <div class="swiper testimoniSwiper pb-14">
      <div class="swiper-wrapper">
        @php
          $testimoni = [
            ['nama'=>'Ahmad Fauzi','peran'=>'Siswa SMK Fatahillah 1 Cilegon','pesan'=>'Proses pendaftaran sangat mudah dan cepat. Saya bisa daftar dari rumah tanpa harus antri panjang. Sistemnya sangat membantu sekali!','avatar'=>'AF','color'=>'bg-blue-500','bintang'=>5],
            ['nama'=>'Siti Rahayu','peran'=>'Orang Tua Siswa SMP Fatahillah','pesan'=>'Saya sangat puas dengan sistem PPDB online ini. Informasi lengkap, mudah dipahami, dan status pendaftaran bisa dipantau kapan saja.','avatar'=>'SR','color'=>'bg-primary-500','bintang'=>5],
            ['nama'=>'Budi Santoso','peran'=>'Siswa SMK Fatahillah 2 Cilegon','pesan'=>'Alhamdulillah diterima di SMK Fatahillah 2. Proses seleksinya transparan dan pengumuman bisa langsung dicek online. Recommended!','avatar'=>'BS','color'=>'bg-purple-500','bintang'=>5],
            ['nama'=>'Dewi Lestari','peran'=>'Orang Tua Siswa SMK Fatahillah 1','pesan'=>'Tidak perlu repot datang ke sekolah untuk mendaftar. Semua bisa dilakukan dari rumah. Notifikasi emailnya juga sangat membantu.','avatar'=>'DL','color'=>'bg-pink-500','bintang'=>5],
            ['nama'=>'Rizki Maulana','peran'=>'Siswa SMK YP Fatahillah Kramatwatu','pesan'=>'Formulir pendaftarannya lengkap tapi mudah diisi. Upload dokumen juga bisa langsung preview dulu sebelum submit.','avatar'=>'RM','color'=>'bg-orange-500','bintang'=>5],
            ['nama'=>'Nurul Hidayah','peran'=>'Guru Bimbingan, SMP Asal','pesan'=>'Sebagai guru BK, saya merekomendasikan PPDB online ini ke siswa saya. Prosesnya profesional dan responsif.','avatar'=>'NH','color'=>'bg-teal-500','bintang'=>5],
            ['nama'=>'Fajar Ramadan','peran'=>'Alumni SMK Fatahillah 1 Cilegon','pesan'=>'Dulu daftar manual repot banget. Sekarang online semua jadi lebih mudah. Adik saya bisa daftar dengan mudah.','avatar'=>'FR','color'=>'bg-indigo-500','bintang'=>5],
            ['nama'=>'Ani Wulandari','peran'=>'Orang Tua Siswa SMK Kramatwatu','pesan'=>'Pelayanan PPDB yang sangat baik. Saat ada pertanyaan langsung dijawab melalui kontak yang tersedia.','avatar'=>'AW','color'=>'bg-rose-500','bintang'=>5],
            ['nama'=>'Dimas Prasetyo','peran'=>'Siswa SMP YP Fatahillah Cilegon','pesan'=>'Keren banget bisa daftar pakai HP. Form ngisi datanya gampang dan ada save otomatis jadi ga khawatir hilang.','avatar'=>'DP','color'=>'bg-cyan-500','bintang'=>5],
            ['nama'=>'Ratna Sari','peran'=>'Orang Tua Siswa SMK Fatahillah 2','pesan'=>'Sistem yang transparan dan terpercaya. Kami bisa memantau setiap perkembangan pendaftaran anak kami secara real-time.','avatar'=>'RS','color'=>'bg-emerald-500','bintang'=>5],
          ];
        @endphp

        @foreach($testimoni as $t)
        <div class="swiper-slide">
          <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 relative h-full">
            <div class="absolute top-6 right-6 text-primary-100">
              <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
              </svg>
            </div>
            <div class="flex gap-1 mb-4">
              @for($i = 0; $i < $t['bintang']; $i++)
              <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
              @endfor
            </div>
            <p class="text-gray-600 text-sm leading-relaxed mb-6 italic">"{{ $t['pesan'] }}"</p>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 {{ $t['color'] }} rounded-full flex items-center justify-center text-white font-bold text-sm">{{ $t['avatar'] }}</div>
              <div>
                <div class="font-semibold text-gray-900 text-sm">{{ $t['nama'] }}</div>
                <div class="text-xs text-gray-500">{{ $t['peran'] }}</div>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      <div class="swiper-pagination mt-4"></div>
    </div>
  </div>
</section>

{{-- ===== FAQ SECTION ===== --}}
<section class="py-24 bg-gray-50">
  <div class="max-w-4xl mx-auto px-6">
    <div class="text-center mb-14">
      <span class="inline-block bg-primary-100 text-primary-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-4">FAQ</span>
      <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Pertanyaan yang Sering Ditanyakan</h2>
      <p class="text-gray-500 max-w-xl mx-auto">Temukan jawaban atas pertanyaan umum seputar proses pendaftaran PPDB.</p>
    </div>
    <div class="space-y-4" id="faq-container">
      @php
        $faqs = [
          ['q' => 'Kapan pendaftaran PPDB 2026/2027 dibuka?', 'a' => 'Pendaftaran PPDB 2026/2027 dibuka mulai 1 Mei hingga 30 Mei 2026. Pastikan Anda mendaftar sebelum batas waktu yang ditentukan.'],
          ['q' => 'Apakah pendaftaran dikenakan biaya?', 'a' => 'Pendaftaran PPDB online tidak dikenakan biaya apapun. Proses pendaftaran sepenuhnya gratis.'],
          ['q' => 'Dokumen apa saja yang wajib diupload?', 'a' => 'Dokumen wajib: Pas Foto, Kartu Keluarga, dan Akta Kelahiran. Dokumen opsional: Ijazah/SKL, SKHUN, dan STL.'],
          ['q' => 'Bagaimana cara mengecek status pendaftaran?', 'a' => 'Anda dapat mengecek status pendaftaran melalui menu "Cek Status" dengan memasukkan nomor pendaftaran yang diterima setelah submit formulir.'],
          ['q' => 'Apakah bisa mendaftar ke lebih dari satu sekolah?', 'a' => 'Setiap calon siswa hanya dapat mendaftar ke satu sekolah dalam satu periode pendaftaran PPDB.'],
          ['q' => 'Apa yang dimaksud dengan jalur Prestasi?', 'a' => 'Jalur Prestasi diperuntukkan bagi siswa yang memiliki prestasi akademik maupun non-akademik. Diperlukan bukti sertifikat atau piagam penghargaan yang valid.'],
          ['q' => 'Apakah ada fitur simpan otomatis di formulir?', 'a' => 'Ya! Formulir pendaftaran dilengkapi fitur simpan otomatis di browser Anda. Data yang sudah diisi tidak akan hilang meskipun halaman di-refresh. Tersedia juga tombol Reset Form jika ingin mengulang dari awal.'],
        ];
      @endphp
      @foreach($faqs as $i => $faq)
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <button onclick="toggleFaq({{ $i }})" class="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-gray-50 transition-colors">
          <span class="font-semibold text-gray-900 text-sm pr-4">{{ $faq['q'] }}</span>
          <svg id="faq-icon-{{ $i }}" class="w-5 h-5 text-primary-500 shrink-0 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <div id="faq-answer-{{ $i }}" class="hidden px-6 pb-5">
          <p class="text-sm text-gray-500 leading-relaxed">{{ $faq['a'] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ===== CTA SECTION ===== --}}
<section class="py-24 bg-white">
  <div class="max-w-5xl mx-auto px-6">
    <div class="relative bg-gradient-to-br from-primary-500 to-primary-700 rounded-3xl p-12 text-center text-white overflow-hidden shadow-2xl">
      <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
      <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
      <div class="relative z-10">
        <span class="inline-block bg-white/20 text-white text-sm font-semibold px-4 py-1.5 rounded-full mb-6">
          🎓 Tahun Ajaran 2026/2027
        </span>
        <h2 class="text-3xl md:text-4xl font-extrabold mb-4">Siap Memulai Perjalanan <br/> Pendidikanmu?</h2>
        <p class="text-white/80 max-w-xl mx-auto mb-8 leading-relaxed">
          Jangan lewatkan kesempatan emas ini. Daftarkan diri sekarang dan jadilah bagian dari keluarga besar Yayasan Fatahillah.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
          <a href="{{ route('daftar.create') }}"
             class="inline-flex items-center gap-2 bg-white text-primary-700 font-bold px-8 py-4 rounded-2xl shadow-xl hover:shadow-2xl hover:scale-105 transition-all">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Daftar Sekarang — Gratis!
          </a>
          <a href="{{ route('status.index') }}"
             class="inline-flex items-center gap-2 bg-white/10 border border-white/30 text-white font-semibold px-8 py-4 rounded-2xl hover:bg-white/20 transition-all">
            Cek Status Pendaftaran
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
  // ===== FAQ TOGGLE =====
  function toggleFaq(index) {
    const answer = document.getElementById('faq-answer-' + index);
    const icon   = document.getElementById('faq-icon-' + index);
    answer.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
  }

  // ===== SWIPER TESTIMONI =====
  new Swiper('.testimoniSwiper', {
    slidesPerView: 1,
    spaceBetween: 20,
    loop: true,
    autoplay: { delay: 4000, disableOnInteraction: false },
    pagination: { el: '.swiper-pagination', clickable: true },
    breakpoints: {
      640:  { slidesPerView: 2 },
      1024: { slidesPerView: 3 },
    }
  });

  // ===== MODAL DETAIL SEKOLAH =====
  const sekolahDetail = {
    smk1cilegon: {
      nama: 'SMK YP Fatahillah 1 Cilegon',
      q: 'SMK YP Fatahillah 1 Cilegon',
      tingkatan: 'SMK',
      logo: '{{ asset("assets/images/logo-SMK_YP_Fatahillah_1_Cilegon.jpeg") }}',
      desc: 'SMK YP Fatahillah 1 Cilegon merupakan sekolah kejuruan unggulan yang berdiri di bawah naungan Yayasan Fatahillah. Sekolah ini memiliki berbagai program keahlian di bidang teknik dan teknologi.',
      jurusan: ['Teknik Komputer dan Jaringan', 'Teknik Mesin', 'Teknik Otomotif', 'Teknik Elektronika'],
      alamat: 'Jl. Fatahillah No.1, Cilegon, Banten',
      akreditasi: 'A',
      color: 'from-blue-500 to-blue-700',
    },
    smk2cilegon: {
      nama: 'SMK YP Fatahillah 2 Cilegon',
      q: 'SMK YP Fatahillah 2 Cilegon',
      tingkatan: 'SMK',
      logo: '{{ asset("assets/images/logo-SMK_YP_Fatahillah_2_Cilegon.png") }}',
      desc: 'SMK YP Fatahillah 2 Cilegon berfokus pada program keahlian bisnis, akuntansi, dan administrasi perkantoran dengan standar nasional.',
      jurusan: ['Akuntansi dan Keuangan', 'Administrasi Perkantoran', 'Bisnis Daring & Pemasaran', 'Perbankan Syariah'],
      alamat: 'Jl. Fatahillah No.2, Cilegon, Banten',
      akreditasi: 'A',
      color: 'from-purple-500 to-purple-700',
    },
    smk1kramatwatu: {
      nama: 'SMK YP Fatahillah 1 Kramatwatu',
      q: 'SMK YP Fatahillah 1 Kramatwatu',
      tingkatan: 'SMK',
      logo: '{{ asset("assets/images/logo-SMK_YP_Fatahillah_1_Kramatwatu.jpeg") }}',
      desc: 'SMK YP Fatahillah 1 Kramatwatu berfokus pada bidang industri dan manufaktur, menghasilkan lulusan yang siap kerja di sektor industri.',
      jurusan: ['Teknik Pengelasan', 'Teknik Pemesinan', 'Teknik Kendaraan Ringan', 'Teknik Instalasi Tenaga Listrik'],
      alamat: 'Jl. Kramatwatu Raya, Serang, Banten',
      akreditasi: 'B',
      color: 'from-orange-500 to-orange-700',
    },
    smpcilegon: {
      nama: 'SMP YP Fatahillah Cilegon',
      q: 'SMP YP Fatahillah Cilegon',
      tingkatan: 'SMP',
      logo: '{{ asset("assets/images/logo-SMP_Fatahillah_Cilegon.jpeg") }}',
      desc: 'SMP YP Fatahillah Cilegon menyediakan pendidikan menengah pertama berkualitas dengan perpaduan kurikulum nasional dan pendidikan karakter islami.',
      jurusan: ['Tidak ada jurusan (SMP)'],
      alamat: 'Jl. Fatahillah Gg.1, Cilegon, Banten',
      akreditasi: 'A',
      color: 'from-teal-500 to-teal-700',
    },
  };

  function showDetailSekolah(key) {
    const s = sekolahDetail[key];
    if (!s) return;
    const jurusanList = s.jurusan.map(j => `<li class="flex items-center gap-2 text-sm text-gray-600"><svg class="w-4 h-4 text-primary-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>${j}</li>`).join('');
    document.getElementById('modal-sekolah-content').innerHTML = `
      <div class="bg-gradient-to-r ${s.color} rounded-2xl p-6 text-white mb-5 -mx-2">
        <div class="flex items-center gap-4">
          <img src="${s.logo}" alt="${s.nama}" class="w-16 h-16 object-contain bg-white rounded-xl p-1" onerror="this.style.display='none'">
          <div>
            <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full font-medium">${s.tingkatan}</span>
            <h3 class="font-bold text-lg mt-1">${s.nama}</h3>
            <span class="text-xs text-white/80">Akreditasi: ${s.akreditasi}</span>
          </div>
        </div>
      </div>
      <p class="text-sm text-gray-600 leading-relaxed mb-4">${s.desc}</p>
      <div class="mb-4">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Alamat</div>
        <p class="text-sm text-gray-700">${s.alamat}</p>
      </div>
      ${s.jurusan[0] !== 'Tidak ada jurusan (SMP)' ? `
      <div class="mb-5">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Program Keahlian</div>
        <ul class="space-y-1.5">${jurusanList}</ul>
      </div>` : ''}
      <a href="{{ route('daftar.create') }}?sekolah=${encodeURIComponent(s.q)}" class="block text-center bg-gradient-to-r ${s.color} text-white font-semibold py-3 rounded-xl hover:opacity-90 transition-opacity text-sm">
        Daftar ke Sekolah Ini
      </a>
    `;
    document.getElementById('modal-sekolah').classList.remove('hidden');
    document.getElementById('modal-sekolah').classList.add('flex');
  }

  function closeModalSekolah() {
    document.getElementById('modal-sekolah').classList.add('hidden');
    document.getElementById('modal-sekolah').classList.remove('flex');
  }
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModalSekolah(); });
</script>
@endpush