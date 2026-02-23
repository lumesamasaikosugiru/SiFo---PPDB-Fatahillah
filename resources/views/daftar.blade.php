@extends('layouts.app')
@section('title', 'Formulir Pendaftaran - PPDB Yayasan Fatahillah 2026/2027')

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-primary-600 to-primary-800 pt-32 pb-24 px-6 text-white text-center">
  <div class="max-w-3xl mx-auto">
    <span class="inline-block bg-white/10 border border-white/20 text-sm font-medium px-4 py-1.5 rounded-full mb-4">
      📝 Formulir Pendaftaran Online 2026/2027
    </span>
    <h1 class="text-3xl md:text-4xl font-extrabold mb-4">Formulir Pendaftaran PPDB</h1>
    <p class="text-white/80 text-lg max-w-xl mx-auto">
      Lengkapi formulir berikut dengan data yang benar dan valid. Data Anda tersimpan otomatis di browser.
    </p>
  </div>
</section>

{{-- Step Indicator --}}
<section class="max-w-4xl mx-auto px-6 -mt-8 relative z-10 mb-8">
  <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6">
    <div class="flex items-center justify-between relative">
      <div class="absolute top-5 left-0 right-0 h-0.5 bg-gray-200 mx-10 z-0">
        <div id="progress-line" class="h-full bg-gradient-to-r from-primary-400 to-primary-600 transition-all duration-500" style="width: 0%"></div>
      </div>
      @php $steps = [['num'=>1,'label'=>'Sekolah'],['num'=>2,'label'=>'Data Diri'],['num'=>3,'label'=>'Jalur'],['num'=>4,'label'=>'Orang Tua'],['num'=>5,'label'=>'Dokumen'],['num'=>6,'label'=>'Review']]; @endphp
      @foreach($steps as $step)
      <div class="flex flex-col items-center relative z-10">
        <div id="step-circle-{{ $step['num'] }}" class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 {{ $step['num'] === 1 ? 'bg-gradient-to-br from-primary-400 to-primary-600 text-white shadow-lg shadow-primary-200' : 'bg-gray-100 text-gray-400' }}">
          <span id="step-num-{{ $step['num'] }}">{{ $step['num'] }}</span>
          <svg id="step-check-{{ $step['num'] }}" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <span id="step-label-{{ $step['num'] }}" class="text-xs font-medium mt-2 transition-colors duration-300 {{ $step['num'] === 1 ? 'text-primary-600' : 'text-gray-400' }} hidden sm:block">{{ $step['label'] }}</span>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Form --}}
<section class="max-w-4xl mx-auto px-6 mb-20">

  {{-- Tombol Reset Form --}}
  <div class="flex justify-end mb-4">
    <button type="button" onclick="confirmResetForm()"
            class="inline-flex items-center gap-2 text-sm text-red-500 hover:text-red-700 border border-red-200 hover:border-red-400 px-4 py-2 rounded-xl transition-all hover:bg-red-50">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
      </svg>
      Reset Form
    </button>
  </div>

  <form id="form-daftar" method="POST" action="{{ route('daftar.store') }}" enctype="multipart/form-data" novalidate>
    @csrf

    {{-- ===== STEP 1: PILIH SEKOLAH ===== --}}
    <div id="step-1" class="step-panel">
      <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-primary-50 to-primary-100 px-8 py-6 border-b border-primary-100">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-primary-500 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
              <h2 class="font-bold text-gray-900 text-lg">Pilih Sekolah & Jurusan</h2>
              <p class="text-sm text-gray-500">Pilih sekolah tujuan dan jurusan yang Anda minati</p>
            </div>
          </div>
        </div>
        <div class="p-8 space-y-6">
          {{-- Pilih Sekolah --}}
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Sekolah Tujuan <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="sekolah-cards">
              @foreach($sekolahs as $sekolah)
              <label class="sekolah-card cursor-pointer">
                <input type="radio" name="sekolah" value="{{ $sekolah->id }}"
                       data-tingkatan="{{ $sekolah->tingkatan }}"
                       data-nama="{{ $sekolah->nama_sekolah }}"
                       class="sr-only sekolah-radio"
                       {{ old('sekolah') == $sekolah->id ? 'checked' : '' }} required>
                <div class="sekolah-card-inner border-2 border-gray-200 rounded-2xl p-4 hover:border-primary-300 hover:bg-primary-50 transition-all">
                  <div class="flex items-start gap-3">
                    <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center shrink-0 overflow-hidden">
                      @php
                        $logoMap = [
                          'SMK YP Fatahillah 1 Cilegon'    => 'logo-SMK_YP_Fatahillah_1_Cilegon.jpeg',
                          'SMK YP Fatahillah 2 Cilegon'    => 'logo-SMK_YP_Fatahillah_2_Cilegon.png',
                          'SMK YP Fatahillah 1 Kramatwatu' => 'logo-SMK_YP_Fatahillah_1_Kramatwatu.jpeg',
                          'SMP YP Fatahillah Cilegon'         => 'logo-SMP_Fatahillah_Cilegon.jpeg',
                        ];
                        $logoFile = $logoMap[$sekolah->nama_sekolah] ?? null;
                      @endphp
                      @if($logoFile)
                      <img src="{{ asset('assets/images/' . $logoFile) }}" alt="{{ $sekolah->nama_sekolah }}" class="w-full h-full object-contain p-1"
                           onerror="this.onerror=null;this.style.display='none'">
                      @else
                      <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                      @endif
                    </div>
                    <div>
                      <div class="font-semibold text-gray-900 text-sm">{{ $sekolah->nama_sekolah }}</div>
                      <div class="text-xs text-gray-500 mt-0.5">{{ $sekolah->tingkatan }}</div>
                      @if($sekolah->kuota)
                      <div class="text-xs text-primary-600 mt-1 font-medium">Kuota: {{ $sekolah->kuota }} siswa</div>
                      @endif
                    </div>
                  </div>
                </div>
              </label>
              @endforeach
            </div>
            <p id="sekolah-error" class="text-red-500 text-xs mt-1 hidden">Pilih sekolah tujuan terlebih dahulu.</p>
          </div>

          {{-- Pilih Jurusan (hanya SMK) --}}
          <div id="jurusan-wrapper" class="hidden transition-all duration-300">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Jurusan <span class="text-red-500">*</span></label>
            <select id="jurusan" name="jurusan"
                    class="w-full px-4 py-3.5 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-transparent bg-gray-50 transition-all">
              <option value="">-- Pilih Jurusan --</option>
            </select>
            <p id="jurusan-error" class="text-red-500 text-xs mt-1 hidden">Pilih jurusan terlebih dahulu.</p>
          </div>

          {{-- Asal Sekolah --}}
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-1">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Asal Sekolah <span class="text-red-500">*</span></label>
              <input type="text" name="asal_sekolah" id="asal_sekolah" value="{{ old('asal_sekolah') }}" placeholder="Nama sekolah asal"
                     class="w-full px-4 py-3.5 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-gray-50 transition-all" required>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Lulus <span class="text-red-500">*</span></label>
              <select name="tahun_lulus" id="tahun_lulus"
                      class="w-full px-4 py-3.5 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-gray-50 transition-all" required>
                <option value="">-- Tahun --</option>
                @for($y = date('Y'); $y >= date('Y') - 10; $y--)
                <option value="{{ $y }}" {{ old('tahun_lulus') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Ijazah <span class="text-red-500">*</span></label>
              <input type="text" name="nomor_ijazah" id="nomor_ijazah" value="{{ old('nomor_ijazah') }}" placeholder="No. Ijazah"
                     class="w-full px-4 py-3.5 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-gray-50 transition-all" required>
            </div>
          </div>
        </div>
        <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end">
          <button type="button" onclick="nextStep()" class="inline-flex items-center gap-2 bg-gradient-to-r from-primary-400 to-primary-600 text-white font-semibold px-8 py-3 rounded-2xl hover:shadow-lg hover:scale-105 transition-all text-sm">
            Selanjutnya <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>
      </div>
    </div>

    {{-- ===== STEP 2: DATA PRIBADI ===== --}}
    <div id="step-2" class="step-panel hidden">
      <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-8 py-6 border-b border-blue-100">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
              <h2 class="font-bold text-gray-900 text-lg">Data Pribadi Siswa</h2>
              <p class="text-sm text-gray-500">Isi data diri sesuai dokumen resmi</p>
            </div>
          </div>
        </div>
        <div class="p-8 space-y-5">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
              <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}" placeholder="Nama lengkap sesuai akta kelahiran"
                     class="w-full px-4 py-3.5 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-gray-50 transition-all" required>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">NISN <span class="text-red-500">*</span></label>
              <input type="text" name="nisn" id="nisn" value="{{ old('nisn') }}" placeholder="10 digit NISN" maxlength="10"
                     class="w-full px-4 py-3.5 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-gray-50 transition-all"
                     oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
              <div class="flex gap-3 mt-1">
                <div class="gender-btn flex-1">
                  <input type="radio" name="jenis_kelamin" id="jk_laki" value="Laki-Laki" {{ old('jenis_kelamin') == 'Laki-Laki' ? 'checked' : '' }} required>
                  <label for="jk_laki">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                      <circle cx="10" cy="14" r="5"/><path stroke-linecap="round" stroke-linejoin="round" d="M19 5l-4.35 4.35M19 5h-4M19 5v4"/>
                    </svg>
                    Laki-laki
                  </label>
                </div>
                <div class="gender-btn flex-1">
                  <input type="radio" name="jenis_kelamin" id="jk_perempuan" value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'checked' : '' }}>
                  <label for="jk_perempuan">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                      <circle cx="12" cy="8" r="5"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 13v8M9 18h6"/>
                    </svg>
                    Perempuan
                  </label>
                </div>
              </div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Tempat Lahir <span class="text-red-500">*</span></label>
              <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ old('tempat_lahir') }}" placeholder="Kota kelahiran"
                     class="w-full px-4 py-3.5 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-gray-50 transition-all" required>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Lahir <span class="text-red-500">*</span></label>
              <input type="text" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}" placeholder="Pilih tanggal lahir" readonly
                     class="w-full px-4 py-3.5 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-gray-50 transition-all cursor-pointer" required>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Agama <span class="text-red-500">*</span></label>
              <select name="agama" id="agama" class="w-full px-4 py-3.5 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-gray-50 transition-all" required>
                <option value="">-- Pilih Agama --</option>
                @foreach(['Islam','Kristen','Katolik','Hindu','Budha','Konghucu'] as $agama)
                <option value="{{ $agama }}" {{ old('agama') == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                @endforeach
              </select>
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap <span class="text-red-500">*</span></label>
              <textarea name="alamat" id="alamat" rows="3" placeholder="Alamat lengkap sesuai KK"
                        class="w-full px-4 py-3.5 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-gray-50 transition-all resize-none" required>{{ old('alamat') }}</textarea>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor HP/WA <span class="text-red-500">*</span></label>
              <div class="relative">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                  <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="08xx xxxx xxxx"
                       oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                       class="w-full pl-11 pr-4 py-3.5 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-gray-50 transition-all" required>
              </div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Email Aktif <span class="text-red-500">*</span></label>
              <div class="relative">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                  <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="email@contoh.com"
                       onkeyup="this.value=this.value.toLowerCase().replace(/[^a-z0-9.@_\-]/g,'')"
                       class="w-full pl-11 pr-4 py-3.5 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-gray-50 transition-all" required>
              </div>
              <p class="text-xs text-amber-600 mt-1.5 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Notifikasi pendaftaran akan dikirim ke email ini
              </p>
            </div>
          </div>
        </div>
        <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-between">
          <button type="button" onclick="prevStep()" class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 font-semibold px-6 py-3 rounded-2xl hover:bg-gray-200 transition-all text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg> Sebelumnya
          </button>
          <button type="button" onclick="nextStep()" class="inline-flex items-center gap-2 bg-gradient-to-r from-primary-400 to-primary-600 text-white font-semibold px-8 py-3 rounded-2xl hover:shadow-lg hover:scale-105 transition-all text-sm">
            Selanjutnya <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>
      </div>
    </div>

    {{-- ===== STEP 3: JALUR PENDAFTARAN ===== --}}
    <div id="step-3" class="step-panel hidden">
      <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-8 py-6 border-b border-purple-100">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
              <h2 class="font-bold text-gray-900 text-lg">Jalur Pendaftaran</h2>
              <p class="text-sm text-gray-500">Pilih jalur pendaftaran yang sesuai</p>
            </div>
          </div>
        </div>
        <div class="p-8 space-y-6">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">Jalur Pendaftaran <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              @php
                $jalurList = [
                  ['value'=>'Regular',  'label'=>'Jalur Regular',  'desc'=>'Pendaftaran umum tanpa syarat khusus', 'icon'=>'📋', 'color'=>'border-blue-200 hover:border-blue-400 hover:bg-blue-50'],
                  ['value'=>'Prestasi', 'label'=>'Jalur Prestasi', 'desc'=>'Memiliki prestasi akademik/non-akademik', 'icon'=>'🏆', 'color'=>'border-yellow-200 hover:border-yellow-400 hover:bg-yellow-50'],
                  ['value'=>'Afirmasi', 'label'=>'Jalur Afirmasi', 'desc'=>'Dari keluarga kurang mampu', 'icon'=>'🤝', 'color'=>'border-green-200 hover:border-green-400 hover:bg-green-50'],
                  ['value'=>'Pindahan', 'label'=>'Jalur Pindahan', 'desc'=>'Pindah dari sekolah lain', 'icon'=>'🔄', 'color'=>'border-orange-200 hover:border-orange-400 hover:bg-orange-50'],
                ];
              @endphp
              @foreach($jalurList as $jalur)
              <label class="jalur-card cursor-pointer">
                <input type="radio" name="jalur_pendaftaran" value="{{ $jalur['value'] }}" {{ old('jalur_pendaftaran') == $jalur['value'] ? 'checked' : '' }} class="sr-only jalur-radio" required>
                <div class="jalur-card-inner border-2 {{ $jalur['color'] }} rounded-2xl p-4 transition-all">
                  <div class="flex items-start gap-3">
                    <span class="text-2xl">{{ $jalur['icon'] }}</span>
                    <div>
                      <div class="font-semibold text-gray-900 text-sm">{{ $jalur['label'] }}</div>
                      <div class="text-xs text-gray-500 mt-0.5">{{ $jalur['desc'] }}</div>
                    </div>
                  </div>
                </div>
              </label>
              @endforeach
            </div>
          </div>
          <div id="ket-jalur-wrapper" class="hidden space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Keterangan <span class="text-red-500">*</span>
                <span id="ket-jalur-hint" class="font-normal text-gray-400 ml-1"></span>
              </label>
              <textarea name="ket_jalur" id="ket_jalur" rows="3" placeholder="Jelaskan keterangan jalur pendaftaran Anda..."
                        class="w-full px-4 py-3.5 border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-gray-50 transition-all resize-none">{{ old('ket_jalur') }}</textarea>
            </div>
            <div id="lampiran-wrapper" class="hidden">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Lampiran Dokumen Pendukung <span class="text-red-500">*</span></label>
              <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center hover:border-primary-300 hover:bg-primary-50 transition-all cursor-pointer" onclick="document.getElementById('file_lampiran').click()">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <p class="text-sm font-medium text-gray-600 mb-1">Klik untuk upload</p>
                <p class="text-xs text-gray-400">PDF, JPG, PNG maks. 20MB</p>
                <p id="lampiran-filename" class="text-xs text-primary-600 font-medium mt-2 hidden"></p>
              </div>
              <input type="file" name="file_lampiran" id="file_lampiran" accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="showFilePreview(this,'prev-lampiran','lampiran-filename')">
              <div id="prev-lampiran" class="mt-3 hidden"></div>
            </div>
          </div>
        </div>
        <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-between">
          <button type="button" onclick="prevStep()" class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 font-semibold px-6 py-3 rounded-2xl hover:bg-gray-200 transition-all text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg> Sebelumnya
          </button>
          <button type="button" onclick="nextStep()" class="inline-flex items-center gap-2 bg-gradient-to-r from-primary-400 to-primary-600 text-white font-semibold px-8 py-3 rounded-2xl hover:shadow-lg hover:scale-105 transition-all text-sm">
            Selanjutnya <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>
      </div>
    </div>

    {{-- ===== STEP 4: DATA ORANG TUA (DINAMIS) ===== --}}
    <div id="step-4" class="step-panel hidden">
      <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-orange-50 to-orange-100 px-8 py-6 border-b border-orange-100">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
              <h2 class="font-bold text-gray-900 text-lg">Data Orang Tua / Wali</h2>
              <p class="text-sm text-gray-500">Minimal 1 data wali wajib diisi</p>
            </div>
          </div>
        </div>
        <div class="p-8">
          <div id="wali-container" class="space-y-6">
            {{-- Wali pertama --}}
            <div class="wali-item bg-gray-50 border border-gray-200 rounded-2xl p-5" data-index="0">
              <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                  <span class="w-7 h-7 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-xs font-bold wali-num">1</span>
                  Data Orang Tua / Wali ke-<span class="wali-num-text">1</span>
                </h3>
                <button type="button" onclick="removeWali(this)" class="remove-wali hidden w-8 h-8 rounded-lg bg-red-100 text-red-500 hover:bg-red-200 flex items-center justify-center transition-colors" title="Hapus data wali ini">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Hubungan <span class="text-red-500">*</span></label>
                  <select name="wali[0][hubungan]" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-white transition-all" required>
                    <option value="">-- Pilih Hubungan --</option>
                    <option value="bapak">Ayah/Bapak</option>
                    <option value="ibu">Ibu</option>
                    <option value="saudara_kandung">Saudara Kandung</option>
                    <option value="saudara_keluarga">Saudara Keluarga</option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                  <input type="text" name="wali[0][nama_wali]" placeholder="Nama lengkap" required
                         class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-white transition-all">
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pekerjaan <span class="text-red-500">*</span></label>
                  <input type="text" name="wali[0][pekerjaan]" placeholder="Pekerjaan" required
                         class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-white transition-all">
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1.5">No. Telepon <span class="text-gray-400 font-normal">(Opsional)</span></label>
                  <input type="text" name="wali[0][notelp_wali]" placeholder="08xx xxxx xxxx"
                         oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                         class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-white transition-all">
                </div>
                <div class="md:col-span-2">
                  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email <span class="text-gray-400 font-normal">(Opsional — untuk notifikasi)</span></label>
                  <input type="email" name="wali[0][email]" placeholder="email@contoh.com"
                         onkeyup="this.value=this.value.toLowerCase().replace(/[^a-z0-9.@_\-]/g,'')"
                         class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-white transition-all">
                </div>
              </div>
            </div>
          </div>

          {{-- Warning minimal 1 --}}
          <p id="wali-warning" class="hidden text-red-500 text-sm mt-3 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Minimal 1 data orang tua/wali wajib diisi.
          </p>

          {{-- Tombol tambah wali --}}
          <button type="button" onclick="addWali()"
                  class="mt-5 inline-flex items-center gap-2 border-2 border-dashed border-orange-300 text-orange-600 hover:bg-orange-50 font-semibold px-5 py-2.5 rounded-2xl transition-all text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Data Wali Lain
          </button>
        </div>
        <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-between">
          <button type="button" onclick="prevStep()" class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 font-semibold px-6 py-3 rounded-2xl hover:bg-gray-200 transition-all text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg> Sebelumnya
          </button>
          <button type="button" onclick="nextStep()" class="inline-flex items-center gap-2 bg-gradient-to-r from-primary-400 to-primary-600 text-white font-semibold px-8 py-3 rounded-2xl hover:shadow-lg hover:scale-105 transition-all text-sm">
            Selanjutnya <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>
      </div>
    </div>

    {{-- ===== STEP 5: UPLOAD DOKUMEN ===== --}}
    <div id="step-5" class="step-panel hidden">
      <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-teal-50 to-teal-100 px-8 py-6 border-b border-teal-100">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
              <h2 class="font-bold text-gray-900 text-lg">Upload Dokumen</h2>
              <p class="text-sm text-gray-500">Pas foto, KK, dan akta kelahiran wajib diupload</p>
            </div>
          </div>
        </div>
        <div class="p-8">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @php
              $dokumenList = [
                ['name'=>'pas_foto', 'label'=>'Pas Foto',       'icon'=>'📷', 'desc'=>'Foto 3x4 background merah',       'required'=>true],
                ['name'=>'kk',       'label'=>'Kartu Keluarga', 'icon'=>'🏠', 'desc'=>'KK yang masih berlaku',            'required'=>true],
                ['name'=>'akta',     'label'=>'Akta Kelahiran', 'icon'=>'📜', 'desc'=>'Akta kelahiran resmi',              'required'=>true],
                ['name'=>'ijazah',   'label'=>'Ijazah / SKL',   'icon'=>'🎓', 'desc'=>'Ijazah atau SKL dari sekolah asal','required'=>false],
                ['name'=>'skhun',    'label'=>'SKHUN',          'icon'=>'📄', 'desc'=>'Surat Keterangan Hasil Ujian',      'required'=>false],
                ['name'=>'stl',      'label'=>'STL',            'icon'=>'📋', 'desc'=>'Surat Tanda Lulus',                'required'=>false],
              ];
            @endphp
            @foreach($dokumenList as $dok)
            <div class="border-2 border-dashed {{ $dok['required'] ? 'border-primary-200' : 'border-gray-200' }} rounded-2xl p-5 hover:border-primary-300 hover:bg-primary-50 transition-all group">
              <div class="flex items-start gap-3 mb-3">
                <span class="text-2xl">{{ $dok['icon'] }}</span>
                <div>
                  <div class="flex items-center gap-2">
                    <div class="font-semibold text-gray-900 text-sm">{{ $dok['label'] }}</div>
                    @if($dok['required'])
                    <span class="text-xs bg-red-100 text-red-600 px-1.5 py-0.5 rounded-full font-medium">Wajib</span>
                    @else
                    <span class="text-xs bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded-full">Opsional</span>
                    @endif
                  </div>
                  <div class="text-xs text-gray-400 mt-0.5">{{ $dok['desc'] }}</div>
                </div>
              </div>
              <div>
                <input type="file" name="{{ $dok['name'] }}" id="file-{{ $dok['name'] }}" accept=".pdf,.jpg,.jpeg,.png"
                       class="hidden" {{ $dok['required'] ? 'required' : '' }}
                       onchange="showFilePreview(this,'prev-{{ $dok['name'] }}','fname-{{ $dok['name'] }}')">
                <button type="button" onclick="document.getElementById('file-{{ $dok['name'] }}').click()"
                        class="w-full flex items-center justify-center gap-2 bg-white border border-gray-200 text-gray-600 text-xs font-medium px-4 py-2.5 rounded-xl hover:border-primary-300 hover:text-primary-600 transition-all">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                  Pilih File
                </button>
                <p id="fname-{{ $dok['name'] }}" class="text-xs text-primary-600 font-medium mt-1.5 text-center hidden"></p>
                {{-- Preview box --}}
                <div id="prev-{{ $dok['name'] }}" class="mt-2 hidden"></div>
              </div>
            </div>
            @endforeach
          </div>
          <div class="mt-6 bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
              <p class="text-sm font-semibold text-amber-800">Catatan Penting</p>
              <p class="text-xs text-amber-700 mt-1 leading-relaxed">Pas foto, Kartu Keluarga, dan Akta Kelahiran wajib diupload. Format: PDF, JPG, JPEG, PNG. Maksimal 20MB per file.</p>
            </div>
          </div>
        </div>
        <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-between">
          <button type="button" onclick="prevStep()" class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 font-semibold px-6 py-3 rounded-2xl hover:bg-gray-200 transition-all text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg> Sebelumnya
          </button>
          <button type="button" onclick="nextStep()" class="inline-flex items-center gap-2 bg-gradient-to-r from-primary-400 to-primary-600 text-white font-semibold px-8 py-3 rounded-2xl hover:shadow-lg hover:scale-105 transition-all text-sm">
            Selanjutnya <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>
      </div>
    </div>

    {{-- ===== STEP 6: REVIEW & SUBMIT ===== --}}
    <div id="step-6" class="step-panel hidden">
      <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-green-50 to-green-100 px-8 py-6 border-b border-green-100">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
              <h2 class="font-bold text-gray-900 text-lg">Review & Submit</h2>
              <p class="text-sm text-gray-500">Periksa kembali data sebelum mengirim</p>
            </div>
          </div>
        </div>
        <div class="p-8 space-y-5">
          {{-- Review cards --}}
          <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
              <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2"><span class="w-6 h-6 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center text-xs font-bold">1</span>Sekolah & Jurusan</h3>
              <button type="button" onclick="goToStep(1)" class="text-xs text-primary-600 hover:underline font-medium">Edit</button>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
              <div><div class="text-xs text-gray-500 mb-0.5">Sekolah Tujuan</div><div class="font-semibold text-gray-900" id="rev-sekolah">-</div></div>
              <div><div class="text-xs text-gray-500 mb-0.5">Jurusan</div><div class="font-semibold text-gray-900" id="rev-jurusan">-</div></div>
              <div><div class="text-xs text-gray-500 mb-0.5">Asal Sekolah</div><div class="font-semibold text-gray-900" id="rev-asal-sekolah">-</div></div>
              <div><div class="text-xs text-gray-500 mb-0.5">Tahun Lulus</div><div class="font-semibold text-gray-900" id="rev-tahun-lulus">-</div></div>
              <div><div class="text-xs text-gray-500 mb-0.5">Nomor Ijazah</div><div class="font-semibold text-gray-900" id="rev-nomor-ijazah">-</div></div>
            </div>
          </div>
          <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
              <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2"><span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-xs font-bold">2</span>Data Pribadi</h3>
              <button type="button" onclick="goToStep(2)" class="text-xs text-primary-600 hover:underline font-medium">Edit</button>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
              <div><div class="text-xs text-gray-500 mb-0.5">Nama Lengkap</div><div class="font-semibold text-gray-900" id="rev-nama">-</div></div>
              <div><div class="text-xs text-gray-500 mb-0.5">NISN</div><div class="font-semibold text-gray-900" id="rev-nisn">-</div></div>
              <div><div class="text-xs text-gray-500 mb-0.5">Jenis Kelamin</div><div class="font-semibold text-gray-900" id="rev-jk">-</div></div>
              <div><div class="text-xs text-gray-500 mb-0.5">Tempat, Tgl Lahir</div><div class="font-semibold text-gray-900" id="rev-ttl">-</div></div>
              <div><div class="text-xs text-gray-500 mb-0.5">Agama</div><div class="font-semibold text-gray-900" id="rev-agama">-</div></div>
              <div><div class="text-xs text-gray-500 mb-0.5">No. HP</div><div class="font-semibold text-gray-900" id="rev-phone">-</div></div>
              <div class="col-span-2"><div class="text-xs text-gray-500 mb-0.5">Email</div><div class="font-semibold text-gray-900" id="rev-email">-</div></div>
              <div class="col-span-2 md:col-span-3"><div class="text-xs text-gray-500 mb-0.5">Alamat</div><div class="font-semibold text-gray-900" id="rev-alamat">-</div></div>
            </div>
          </div>
          <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
              <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2"><span class="w-6 h-6 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center text-xs font-bold">3</span>Jalur Pendaftaran</h3>
              <button type="button" onclick="goToStep(3)" class="text-xs text-primary-600 hover:underline font-medium">Edit</button>
            </div>
            <div class="grid grid-cols-2 gap-3 text-sm">
              <div><div class="text-xs text-gray-500 mb-0.5">Jalur</div><div class="font-semibold text-gray-900" id="rev-jalur">-</div></div>
              <div><div class="text-xs text-gray-500 mb-0.5">Keterangan</div><div class="font-semibold text-gray-900" id="rev-ket-jalur">-</div></div>
            </div>
          </div>
          <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
              <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2"><span class="w-6 h-6 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-xs font-bold">4</span>Data Orang Tua</h3>
              <button type="button" onclick="goToStep(4)" class="text-xs text-primary-600 hover:underline font-medium">Edit</button>
            </div>
            <div id="rev-wali" class="space-y-2 text-sm"></div>
          </div>
          <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
              <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2"><span class="w-6 h-6 bg-teal-100 text-teal-600 rounded-lg flex items-center justify-center text-xs font-bold">5</span>Dokumen</h3>
              <button type="button" onclick="goToStep(5)" class="text-xs text-primary-600 hover:underline font-medium">Edit</button>
            </div>
            <div id="rev-dokumen" class="grid grid-cols-2 md:grid-cols-3 gap-3"></div>
          </div>
          {{-- Persetujuan --}}
          <div class="bg-primary-50 border border-primary-200 rounded-2xl p-5">
            <label class="flex items-start gap-3 cursor-pointer">
              <input type="checkbox" id="persetujuan" class="w-4 h-4 mt-0.5 text-primary-500 rounded focus:ring-primary-300" required>
              <span class="text-sm text-gray-700 leading-relaxed">
                Saya menyatakan bahwa semua data yang saya isi adalah <strong>benar dan dapat dipertanggungjawabkan</strong>. Saya bersedia menerima konsekuensi apabila data yang diberikan tidak sesuai dengan dokumen asli.
              </span>
            </label>
          </div>
        </div>
        <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
          <button type="button" onclick="prevStep()" class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 font-semibold px-6 py-3 rounded-2xl hover:bg-gray-200 transition-all text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg> Sebelumnya
          </button>
          <button type="submit" id="btn-submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-green-500 to-green-700 text-white font-bold px-10 py-3 rounded-2xl hover:shadow-xl hover:scale-105 transition-all text-sm">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Submit Pendaftaran
          </button>
        </div>
      </div>
    </div>

  </form>
</section>

@endsection

@push('scripts')
<script>
  // =============================================
  // DATA JURUSAN DARI LARAVEL
  // =============================================
  const allJurusan = @json(\App\Models\Jurusan::where('is_active', 1)->get());
  const allSekolah  = @json($sekolahs);

  // =============================================
  // LOCALSTORAGE CACHE KEY
  // =============================================
  const CACHE_KEY     = 'ppdb_form_cache_2026';
  const FILE_DB_NAME  = 'ppdb_files_2026';
  const FILE_DB_STORE = 'dokumen';

  // =============================================
  // INDEXEDDB — simpan file binary di browser
  // Persist sampai: reset form / submit selesai / user clear storage
  // =============================================
  let fileDB = null;

  function openFileDB() {
    return new Promise((resolve, reject) => {
      if (fileDB) { resolve(fileDB); return; }
      const req = indexedDB.open(FILE_DB_NAME, 1);
      req.onupgradeneeded = e => {
        e.target.result.createObjectStore(FILE_DB_STORE);
      };
      req.onsuccess = e => { fileDB = e.target.result; resolve(fileDB); };
      req.onerror   = e => reject(e.target.error);
    });
  }

  async function saveFileToDB(key, file) {
    const db = await openFileDB();
    return new Promise((resolve, reject) => {
      const tx = db.transaction(FILE_DB_STORE, 'readwrite');
      tx.objectStore(FILE_DB_STORE).put(file, key);
      tx.oncomplete = () => resolve();
      tx.onerror    = e => reject(e.target.error);
    });
  }

  async function getFileFromDB(key) {
    const db = await openFileDB();
    return new Promise((resolve, reject) => {
      const tx  = db.transaction(FILE_DB_STORE, 'readonly');
      const req = tx.objectStore(FILE_DB_STORE).get(key);
      req.onsuccess = e => resolve(e.target.result || null);
      req.onerror   = e => reject(e.target.error);
    });
  }

  async function deleteFileFromDB(key) {
    const db = await openFileDB();
    return new Promise((resolve) => {
      const tx = db.transaction(FILE_DB_STORE, 'readwrite');
      tx.objectStore(FILE_DB_STORE).delete(key);
      tx.oncomplete = () => resolve();
    });
  }

  async function clearAllFilesFromDB() {
    const db = await openFileDB();
    return new Promise((resolve) => {
      const tx = db.transaction(FILE_DB_STORE, 'readwrite');
      tx.objectStore(FILE_DB_STORE).clear();
      tx.oncomplete = () => resolve();
    });
  }

  // Semua field dokumen yang di-track
  const ALL_DOK_FIELDS = ['pas_foto','kk','akta','ijazah','skhun','stl','file_lampiran'];

  // =============================================
  // STEP WIZARD
  // =============================================
  let currentStep  = 1;
  const totalSteps = 6;

  function showStep(step) {
    document.querySelectorAll('.step-panel').forEach(p => p.classList.add('hidden'));
    document.getElementById('step-' + step).classList.remove('hidden');
    for (let i = 1; i <= totalSteps; i++) {
      const circle = document.getElementById('step-circle-' + i);
      const label  = document.getElementById('step-label-' + i);
      const num    = document.getElementById('step-num-' + i);
      const check  = document.getElementById('step-check-' + i);
      if (i < step) {
        circle.className = 'w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 bg-green-500 text-white shadow-lg';
        num.classList.add('hidden'); check.classList.remove('hidden');
        label.className = 'text-xs font-medium mt-2 transition-colors duration-300 text-green-600 hidden sm:block';
      } else if (i === step) {
        circle.className = 'w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 bg-gradient-to-br from-primary-400 to-primary-600 text-white shadow-lg shadow-primary-200';
        num.classList.remove('hidden'); check.classList.add('hidden');
        label.className = 'text-xs font-medium mt-2 transition-colors duration-300 text-primary-600 hidden sm:block';
      } else {
        circle.className = 'w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 bg-gray-100 text-gray-400';
        num.classList.remove('hidden'); check.classList.add('hidden');
        label.className = 'text-xs font-medium mt-2 transition-colors duration-300 text-gray-400 hidden sm:block';
      }
    }
    const progress = ((step - 1) / (totalSteps - 1)) * 100;
    document.getElementById('progress-line').style.width = progress + '%';
    window.scrollTo({ top: 0, behavior: 'smooth' });
    if (step === totalSteps) fillResume();
  }

  function nextStep() {
    if (validateStep(currentStep)) {
      saveCache();
      if (currentStep < totalSteps) { currentStep++; showStep(currentStep); }
    }
  }
  function prevStep() {
    if (currentStep > 1) { currentStep--; showStep(currentStep); }
  }
  function goToStep(step) { currentStep = step; showStep(step); }

  // =============================================
  // VALIDASI PER STEP
  // =============================================
  function validateStep(step) {
    let valid = true;
    if (step === 1) {
      const sekolahChecked = document.querySelector('input[name="sekolah"]:checked');
      if (!sekolahChecked) { document.getElementById('sekolah-error').classList.remove('hidden'); valid = false; }
      else document.getElementById('sekolah-error').classList.add('hidden');

      const jw = document.getElementById('jurusan-wrapper');
      if (!jw.classList.contains('hidden')) {
        const j = document.getElementById('jurusan').value;
        if (!j) { document.getElementById('jurusan-error').classList.remove('hidden'); valid = false; }
        else document.getElementById('jurusan-error').classList.add('hidden');
      }
      if (!document.getElementById('asal_sekolah').value.trim()) { showFieldError('asal_sekolah','Asal sekolah wajib diisi'); valid = false; } else clearFieldError('asal_sekolah');
      if (!document.getElementById('tahun_lulus').value) { showFieldError('tahun_lulus','Tahun lulus wajib dipilih'); valid = false; } else clearFieldError('tahun_lulus');
      if (!document.getElementById('nomor_ijazah').value.trim()) { showFieldError('nomor_ijazah','Nomor ijazah wajib diisi'); valid = false; } else clearFieldError('nomor_ijazah');
    }
    if (step === 2) {
      ['nama_lengkap','nisn','tempat_lahir','tanggal_lahir','agama','alamat','phone','email'].forEach(id => {
        const el = document.getElementById(id);
        if (!el.value.trim()) { showFieldError(id, 'Field ini wajib diisi'); valid = false; } else clearFieldError(id);
      });
      if (!document.querySelector('input[name="jenis_kelamin"]:checked')) {
        Swal.fire({ icon:'warning', title:'Perhatian', text:'Jenis kelamin wajib dipilih.' }); valid = false;
      }
      const email = document.getElementById('email').value;
      if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showFieldError('email','Format email tidak valid'); valid = false; }
      const nisn = document.getElementById('nisn').value;
      if (nisn && nisn.length !== 10) { showFieldError('nisn','NISN harus 10 digit'); valid = false; }
    }
    if (step === 3) {
      const jalur = document.querySelector('input[name="jalur_pendaftaran"]:checked');
      if (!jalur) {
        Swal.fire({ icon:'warning', title:'Perhatian', text:'Jalur pendaftaran wajib dipilih.' }); valid = false;
      } else if (jalur.value !== 'Regular') {
        const ket = document.getElementById('ket_jalur').value.trim();
        if (!ket) { showFieldError('ket_jalur','Keterangan wajib diisi'); valid = false; } else clearFieldError('ket_jalur');
        if ((jalur.value === 'Prestasi' || jalur.value === 'Afirmasi')) {
          const f = document.getElementById('file_lampiran');
          if (!f.files || !f.files.length) {
            Swal.fire({ icon:'warning', title:'Perhatian', text:'Lampiran dokumen wajib diupload untuk jalur ' + jalur.value + '.' }); valid = false;
          }
        }
      }
    }
    if (step === 4) {
      const items = document.querySelectorAll('.wali-item');
      if (items.length === 0) { document.getElementById('wali-warning').classList.remove('hidden'); valid = false; return valid; }
      document.getElementById('wali-warning').classList.add('hidden');
      let waliValid = true;
      items.forEach((item, idx) => {
        const n = item.querySelector('input[name$="[nama_wali]"]');
        const h = item.querySelector('select[name$="[hubungan]"]');
        const p = item.querySelector('input[name$="[pekerjaan]"]');
        if (!n.value.trim() || !p.value.trim() || !h.value) { waliValid = false; }
      });
      if (!waliValid) { Swal.fire({ icon:'warning', title:'Perhatian', text:'Nama, hubungan, dan pekerjaan wali wajib diisi.' }); valid = false; }
    }
    if (step === 5) {
      const required = ['pas_foto','kk','akta'];
      required.forEach(name => {
        const f = document.getElementById('file-' + name);
        if (!f.files || !f.files.length) { Swal.fire({ icon:'warning', title:'Dokumen Wajib', text: (name==='pas_foto'?'Pas Foto':name==='kk'?'Kartu Keluarga':'Akta Kelahiran') + ' wajib diupload.' }); valid = false; }
      });
      document.querySelectorAll('#step-5 input[type="file"]').forEach(inp => {
        if (inp.files && inp.files.length > 0 && inp.files[0].size > 20*1024*1024) {
          Swal.fire({ icon:'error', title:'File Terlalu Besar', text:'File ' + inp.name + ' melebihi 20MB.' }); valid = false;
        }
      });
    }
    if (step === 6) {
      if (!document.getElementById('persetujuan').checked) {
        Swal.fire({ icon:'warning', title:'Perhatian', text:'Anda harus menyetujui pernyataan di atas sebelum submit.' }); valid = false;
      }
    }
    return valid;
  }

  // =============================================
  // HELPER: ERROR FIELD
  // =============================================
  function showFieldError(fieldId, msg) {
    const el = document.getElementById(fieldId); if (!el) return;
    el.classList.add('border-red-300','bg-red-50'); el.classList.remove('border-gray-200','bg-gray-50');
    const old = document.getElementById('err-'+fieldId); if (old) old.remove();
    const e = document.createElement('p'); e.id='err-'+fieldId; e.className='text-red-500 text-xs mt-1'; e.textContent=msg;
    el.parentNode.appendChild(e);
  }
  function clearFieldError(fieldId) {
    const el = document.getElementById(fieldId); if (!el) return;
    el.classList.remove('border-red-300','bg-red-50'); el.classList.add('border-gray-200','bg-gray-50');
    const old = document.getElementById('err-'+fieldId); if (old) old.remove();
  }

  // =============================================
  // SEKOLAH CARD SELECTION
  // =============================================
  document.querySelectorAll('.sekolah-radio').forEach(radio => {
    radio.addEventListener('change', function () {
      document.querySelectorAll('.sekolah-card-inner').forEach(c => { c.classList.remove('border-primary-400','bg-primary-50'); c.classList.add('border-gray-200'); });
      this.closest('.sekolah-card').querySelector('.sekolah-card-inner').classList.add('border-primary-400','bg-primary-50');
      this.closest('.sekolah-card').querySelector('.sekolah-card-inner').classList.remove('border-gray-200');
      document.getElementById('sekolah-error').classList.add('hidden');
      const tingkatan = this.dataset.tingkatan;
      const sekolahId = this.value;
      const jw = document.getElementById('jurusan-wrapper');
      const js = document.getElementById('jurusan');
      if (tingkatan === 'SMK') {
        jw.classList.remove('hidden');
        js.innerHTML = '<option value="">-- Pilih Jurusan --</option>';
        allJurusan.filter(j => j.sekolah_id == sekolahId).forEach(j => {
          const o = document.createElement('option'); o.value = j.id; o.textContent = j.nama_jurusan; js.appendChild(o);
        });
      } else { jw.classList.add('hidden'); js.innerHTML=''; js.value=''; }
    });
  });

  // =============================================
  // JALUR CARD SELECTION
  // =============================================
  document.querySelectorAll('.jalur-radio').forEach(radio => {
    radio.addEventListener('change', function () {
      document.querySelectorAll('.jalur-card-inner').forEach(c => c.classList.remove('border-blue-400','bg-blue-50','border-yellow-400','bg-yellow-50','border-green-400','bg-green-50','border-orange-400','bg-orange-50'));
      const colorMap = { Regular:['border-blue-400','bg-blue-50'], Prestasi:['border-yellow-400','bg-yellow-50'], Afirmasi:['border-green-400','bg-green-50'], Pindahan:['border-orange-400','bg-orange-50'] };
      const sel = this.closest('.jalur-card').querySelector('.jalur-card-inner');
      if (colorMap[this.value]) sel.classList.add(...colorMap[this.value]);
      const kw = document.getElementById('ket-jalur-wrapper');
      const lw = document.getElementById('lampiran-wrapper');
      const kh = document.getElementById('ket-jalur-hint');
      const ki = document.getElementById('ket_jalur');
      const li = document.getElementById('file_lampiran');
      if (this.value === 'Regular') { kw.classList.add('hidden'); lw.classList.add('hidden'); ki.removeAttribute('required'); li.removeAttribute('required'); }
      else if (this.value === 'Pindahan') { kw.classList.remove('hidden'); lw.classList.add('hidden'); kh.textContent='(Jelaskan alasan pindah sekolah)'; ki.setAttribute('required','required'); li.removeAttribute('required'); }
      else { kw.classList.remove('hidden'); lw.classList.remove('hidden'); kh.textContent = this.value==='Prestasi'?'(Jelaskan prestasi yang dimiliki)':'(Jelaskan kondisi ekonomi keluarga)'; ki.setAttribute('required','required'); li.setAttribute('required','required'); }
    });
  });

  // =============================================
  // PREVIEW FILE (JPG/PNG/PDF)
  // =============================================
  // Render preview dari File object atau Blob
  function renderPreview(file, previewId, filenameId) {
    const fname = document.getElementById(filenameId);
    const prev  = document.getElementById(previewId);
    if (!prev) return;

    if (fname) { fname.textContent = '✓ ' + file.name; fname.classList.remove('hidden'); }

    prev.innerHTML = '';
    prev.classList.remove('hidden');

    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = e => {
        prev.innerHTML = `
          <div class="mt-3 rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between px-3 py-1.5 bg-gray-100 border-b border-gray-200">
              <span class="text-xs text-gray-500 font-medium truncate max-w-[200px]">🖼️ ${file.name}</span>
              <a href="${e.target.result}" target="_blank" class="text-xs text-primary-600 hover:underline shrink-0 ml-2">Buka ↗</a>
            </div>
            <img src="${e.target.result}" alt="Preview" class="w-full max-h-56 object-contain bg-white p-2">
          </div>`;
      };
      reader.readAsDataURL(file);

    } else if (file.type === 'application/pdf') {
      const blobUrl = URL.createObjectURL(file);
      prev.innerHTML = `
        <div class="mt-3 rounded-xl overflow-hidden border border-gray-200">
          <div class="flex items-center justify-between px-3 py-1.5 bg-red-50 border-b border-red-100">
            <span class="text-xs text-red-700 font-medium truncate max-w-[200px]">📄 ${file.name}</span>
            <a href="${blobUrl}" target="_blank" class="text-xs text-red-600 hover:underline shrink-0 ml-2">Buka ↗</a>
          </div>
          <iframe src="${blobUrl}" class="w-full bg-white" style="height:220px;border:none;" title="Preview PDF"></iframe>
        </div>`;

    } else {
      prev.innerHTML = `
        <div class="mt-3 flex items-center gap-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-600">
          <svg class="w-5 h-5 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <span class="truncate">${file.name}</span>
        </div>`;
    }
  }

  // Warna box saat file dipilih
  function markBoxSelected(input) {
    const box = input.closest('.border-dashed');
    if (box) {
      box.classList.add('border-green-400','bg-green-50');
      box.classList.remove('border-gray-200','border-primary-200','hover:border-primary-300','hover:bg-primary-50');
    }
  }

  // Dipanggil saat user pilih file dari input — simpan ke IndexedDB lalu render
  function showFilePreview(input, previewId, filenameId) {
    if (!input.files || !input.files.length) return;
    const file = input.files[0];

    if (file.size > 20 * 1024 * 1024) {
      Swal.fire({ icon:'error', title:'File Terlalu Besar', text:'File melebihi ukuran maksimal 20MB.' });
      input.value = ''; return;
    }

    markBoxSelected(input);
    renderPreview(file, previewId, filenameId);

    // Simpan file ke IndexedDB dengan key = nama field input
    const fieldKey = input.name;
    saveFileToDB(fieldKey, file).catch(err => console.warn('IndexedDB save error:', err));
  }

  // =============================================
  // WALI DINAMIS
  // =============================================
  let waliCount = 1;

  function addWali() {
    const idx = waliCount++;
    const container = document.getElementById('wali-container');
    const div = document.createElement('div');
    div.className = 'wali-item bg-gray-50 border border-gray-200 rounded-2xl p-5';
    div.dataset.index = idx;
    div.innerHTML = `
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
          <span class="w-7 h-7 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-xs font-bold wali-num">${idx+1}</span>
          Data Orang Tua / Wali ke-<span class="wali-num-text">${idx+1}</span>
        </h3>
        <button type="button" onclick="removeWali(this)" class="remove-wali w-8 h-8 rounded-lg bg-red-100 text-red-500 hover:bg-red-200 flex items-center justify-center transition-colors">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Hubungan <span class="text-red-500">*</span></label>
          <select name="wali[${idx}][hubungan]" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-white" required>
            <option value="">-- Pilih --</option>
            <option value="bapak">Ayah/Bapak</option>
            <option value="ibu">Ibu</option>
            <option value="saudara_kandung">Saudara Kandung</option>
            <option value="saudara_keluarga">Saudara Keluarga</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
          <input type="text" name="wali[${idx}][nama_wali]" placeholder="Nama lengkap" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-white">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pekerjaan <span class="text-red-500">*</span></label>
          <input type="text" name="wali[${idx}][pekerjaan]" placeholder="Pekerjaan" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-white">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">No. Telepon <span class="text-gray-400 font-normal">(Opsional)</span></label>
          <input type="text" name="wali[${idx}][notelp_wali]" placeholder="08xx xxxx xxxx" oninput="this.value=this.value.replace(/[^0-9]/g,'')" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-white">
        </div>
        <div class="md:col-span-2">
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email <span class="text-gray-400 font-normal">(Opsional)</span></label>
          <input type="email" name="wali[${idx}][email]" placeholder="email@contoh.com" onkeyup="this.value=this.value.toLowerCase().replace(/[^a-z0-9.@_\\-]/g,'')" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-white">
        </div>
      </div>`;
    container.appendChild(div);
    updateRemoveButtons();
  }

  function removeWali(btn) {
    const items = document.querySelectorAll('.wali-item');
    if (items.length <= 1) { Swal.fire({ icon:'info', title:'Perhatian', text:'Minimal 1 data wali harus ada.' }); return; }
    btn.closest('.wali-item').remove();
    updateRemoveButtons();
  }

  function updateRemoveButtons() {
    const items = document.querySelectorAll('.wali-item');
    items.forEach((item, i) => {
      const btn = item.querySelector('.remove-wali');
      btn.classList.toggle('hidden', items.length <= 1);
      item.querySelector('.wali-num').textContent = i + 1;
      item.querySelector('.wali-num-text').textContent = i + 1;
    });
  }

  // =============================================
  // FILL RESUME STEP 6
  // =============================================
  function fillResume() {
    const sr = document.querySelector('input[name="sekolah"]:checked');
    document.getElementById('rev-sekolah').textContent = sr ? sr.closest('.sekolah-card').querySelector('.font-semibold').textContent.trim() : '-';
    const js = document.getElementById('jurusan');
    document.getElementById('rev-jurusan').textContent = js && js.value ? js.options[js.selectedIndex].text : 'Tidak Ada (SMP)';
    document.getElementById('rev-asal-sekolah').textContent = document.getElementById('asal_sekolah').value || '-';
    document.getElementById('rev-tahun-lulus').textContent  = document.getElementById('tahun_lulus').value || '-';
    document.getElementById('rev-nomor-ijazah').textContent = document.getElementById('nomor_ijazah').value || '-';
    document.getElementById('rev-nama').textContent    = document.getElementById('nama_lengkap').value || '-';
    document.getElementById('rev-nisn').textContent    = document.getElementById('nisn').value || '-';
    const jk = document.querySelector('input[name="jenis_kelamin"]:checked');
    document.getElementById('rev-jk').textContent     = jk ? jk.value : '-';
    document.getElementById('rev-ttl').textContent    = (document.getElementById('tempat_lahir').value || '-') + ', ' + (document.getElementById('tanggal_lahir').value || '-');
    document.getElementById('rev-agama').textContent  = document.getElementById('agama').value || '-';
    document.getElementById('rev-phone').textContent  = document.getElementById('phone').value || '-';
    document.getElementById('rev-email').textContent  = document.getElementById('email').value || '-';
    document.getElementById('rev-alamat').textContent = document.getElementById('alamat').value || '-';
    const jr = document.querySelector('input[name="jalur_pendaftaran"]:checked');
    document.getElementById('rev-jalur').textContent     = jr ? jr.value : '-';
    document.getElementById('rev-ket-jalur').textContent = document.getElementById('ket_jalur').value || '-';

    // Wali
    const revWali = document.getElementById('rev-wali');
    revWali.innerHTML = '';
    document.querySelectorAll('.wali-item').forEach((item, i) => {
      const n = item.querySelector('input[name$="[nama_wali]"]').value || '-';
      const h = item.querySelector('select[name$="[hubungan]"]');
      const hText = h.options[h.selectedIndex]?.text || '-';
      const p = item.querySelector('input[name$="[pekerjaan]"]').value || '-';
      revWali.innerHTML += `<div class="grid grid-cols-3 gap-2 p-3 bg-white rounded-xl border border-gray-100 text-xs"><div><span class="text-gray-400">Wali ${i+1}</span><div class="font-semibold text-gray-800">${n}</div></div><div><span class="text-gray-400">Hubungan</span><div class="font-semibold text-gray-800">${hText}</div></div><div><span class="text-gray-400">Pekerjaan</span><div class="font-semibold text-gray-800">${p}</div></div></div>`;
    });

    // Dokumen
    const dokNames = { pas_foto:'Pas Foto', kk:'Kartu Keluarga', akta:'Akta Kelahiran', ijazah:'Ijazah', skhun:'SKHUN', stl:'STL' };
    const rd = document.getElementById('rev-dokumen');
    rd.innerHTML = '';
    let hasDok = false;
    Object.entries(dokNames).forEach(([k,l]) => {
      const inp = document.getElementById('file-' + k);
      if (inp && inp.files && inp.files.length) {
        hasDok = true;
        rd.innerHTML += `<div class="flex items-center gap-2 p-2.5 bg-green-50 border border-green-100 rounded-xl"><svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span class="text-xs font-medium text-green-800">${l}</span></div>`;
      }
    });
    if (!hasDok) rd.innerHTML = '<p class="text-sm text-gray-400 col-span-3">Belum ada dokumen diupload</p>';
  }

  // =============================================
  // LOCALSTORAGE CACHE (simpan & restore form)
  // =============================================
  function saveCache() {
    // Kumpulkan data semua wali
    const waliData = [];
    document.querySelectorAll('.wali-item').forEach((item) => {
      waliData.push({
        hubungan:   item.querySelector('select[name*="[hubungan]"]')?.value || '',
        nama_wali:  item.querySelector('input[name*="[nama_wali]"]')?.value || '',
        pekerjaan:  item.querySelector('input[name*="[pekerjaan]"]')?.value || '',
        notelp_wali: item.querySelector('input[name*="[notelp_wali]"]')?.value || '',
        email:      item.querySelector('input[name*="[email]"]')?.value || '',
      });
    });

    const data = {
      sekolah:       document.querySelector('input[name="sekolah"]:checked')?.value || '',
      jurusan:       document.getElementById('jurusan')?.value || '',
      asal_sekolah:  document.getElementById('asal_sekolah').value,
      tahun_lulus:   document.getElementById('tahun_lulus').value,
      nomor_ijazah:  document.getElementById('nomor_ijazah').value,
      nama_lengkap:  document.getElementById('nama_lengkap').value,
      nisn:          document.getElementById('nisn').value,
      jenis_kelamin: document.querySelector('input[name="jenis_kelamin"]:checked')?.value || '',
      tempat_lahir:  document.getElementById('tempat_lahir').value,
      tanggal_lahir: document.getElementById('tanggal_lahir').value,
      agama:         document.getElementById('agama').value,
      alamat:        document.getElementById('alamat').value,
      phone:         document.getElementById('phone').value,
      email:         document.getElementById('email').value,
      jalur:         document.querySelector('input[name="jalur_pendaftaran"]:checked')?.value || '',
      ket_jalur:     document.getElementById('ket_jalur').value,
      wali:          waliData,
    };
    try { localStorage.setItem(CACHE_KEY, JSON.stringify(data)); } catch(e) {}
  }

  function loadCache() {
    try {
      const raw = localStorage.getItem(CACHE_KEY);
      if (!raw) return;
      const d = JSON.parse(raw);

      if (d.asal_sekolah)  document.getElementById('asal_sekolah').value  = d.asal_sekolah;
      if (d.tahun_lulus)   document.getElementById('tahun_lulus').value   = d.tahun_lulus;
      if (d.nomor_ijazah)  document.getElementById('nomor_ijazah').value  = d.nomor_ijazah;
      if (d.nama_lengkap)  document.getElementById('nama_lengkap').value  = d.nama_lengkap;
      if (d.nisn)          document.getElementById('nisn').value          = d.nisn;
      if (d.tempat_lahir)  document.getElementById('tempat_lahir').value  = d.tempat_lahir;
      if (d.tanggal_lahir) document.getElementById('tanggal_lahir').value = d.tanggal_lahir;
      if (d.agama)         document.getElementById('agama').value         = d.agama;
      if (d.alamat)        document.getElementById('alamat').value        = d.alamat;
      if (d.phone)         document.getElementById('phone').value         = d.phone;
      if (d.email)         document.getElementById('email').value         = d.email;
      if (d.ket_jalur)     document.getElementById('ket_jalur').value     = d.ket_jalur;

      if (d.sekolah) {
        const r = document.querySelector(`input[name="sekolah"][value="${d.sekolah}"]`);
        if (r) {
          r.checked = true;
          // Update visual highlight card
          document.querySelectorAll('.sekolah-card-inner').forEach(c => {
            c.classList.remove('border-primary-400','bg-primary-50');
            c.classList.add('border-gray-200');
          });
          const ci = r.closest('.sekolah-card')?.querySelector('.sekolah-card-inner');
          if (ci) { ci.classList.add('border-primary-400','bg-primary-50'); ci.classList.remove('border-gray-200'); }
          r.dispatchEvent(new Event('change'));
          setTimeout(() => { if (d.jurusan) document.getElementById('jurusan').value = d.jurusan; }, 150);
        }
      }
      if (d.jenis_kelamin) {
        const jkr = document.querySelector(`input[name="jenis_kelamin"][value="${d.jenis_kelamin}"]`);
        if (jkr) jkr.checked = true;
      }
      if (d.jalur) {
        const jr = document.querySelector(`input[name="jalur_pendaftaran"][value="${d.jalur}"]`);
        if (jr) { jr.checked = true; jr.dispatchEvent(new Event('change')); }
      }
      // Restore data wali
      if (d.wali && Array.isArray(d.wali) && d.wali.length > 0) {
        const container = document.getElementById('wali-container');
        // Isi wali pertama (sudah ada di HTML)
        const firstItem = container.querySelector('.wali-item');
        if (firstItem && d.wali[0]) {
          const w = d.wali[0];
          const hubEl = firstItem.querySelector('select[name*="[hubungan]"]');
          if (hubEl && w.hubungan) hubEl.value = w.hubungan;
          const namaEl = firstItem.querySelector('input[name*="[nama_wali]"]');
          if (namaEl && w.nama_wali) namaEl.value = w.nama_wali;
          const pkrEl = firstItem.querySelector('input[name*="[pekerjaan]"]');
          if (pkrEl && w.pekerjaan) pkrEl.value = w.pekerjaan;
          const telpEl = firstItem.querySelector('input[name*="[notelp_wali]"]');
          if (telpEl && w.notelp_wali) telpEl.value = w.notelp_wali;
          const emailEl = firstItem.querySelector('input[name*="[email]"]');
          if (emailEl && w.email) emailEl.value = w.email;
        }
        // Tambah wali berikutnya jika ada
        for (let i = 1; i < d.wali.length; i++) {
          addWali();
          const items = container.querySelectorAll('.wali-item');
          const item = items[items.length - 1];
          const w = d.wali[i];
          const hubEl = item.querySelector('select[name*="[hubungan]"]');
          if (hubEl && w.hubungan) hubEl.value = w.hubungan;
          const namaEl = item.querySelector('input[name*="[nama_wali]"]');
          if (namaEl && w.nama_wali) namaEl.value = w.nama_wali;
          const pkrEl = item.querySelector('input[name*="[pekerjaan]"]');
          if (pkrEl && w.pekerjaan) pkrEl.value = w.pekerjaan;
          const telpEl = item.querySelector('input[name*="[notelp_wali]"]');
          if (telpEl && w.notelp_wali) telpEl.value = w.notelp_wali;
          const emailEl = item.querySelector('input[name*="[email]"]');
          if (emailEl && w.email) emailEl.value = w.email;
        }
      }
    } catch(e) {}
  }

  function confirmResetForm() {
    Swal.fire({
      title: 'Reset Formulir?',
      text: 'Semua data yang sudah Anda isi akan dihapus. Tindakan ini tidak dapat dibatalkan.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Ya, Reset!',
      cancelButtonText: 'Batal'
    }).then(result => {
      if (result.isConfirmed) {
        try { localStorage.removeItem(CACHE_KEY); } catch(e) {}
        clearAllFilesFromDB().catch(() => {});
        document.getElementById('form-daftar').reset();
        // Reset card visuals
        document.querySelectorAll('.sekolah-card-inner').forEach(c => { c.classList.remove('border-primary-400','bg-primary-50'); c.classList.add('border-gray-200'); });
        document.querySelectorAll('.jalur-card-inner').forEach(c => c.classList.remove('border-blue-400','bg-blue-50','border-yellow-400','bg-yellow-50','border-green-400','bg-green-50','border-orange-400','bg-orange-50'));
        document.getElementById('jurusan-wrapper').classList.add('hidden');
        document.getElementById('ket-jalur-wrapper').classList.add('hidden');
        // Reset wali
        const waliContainer = document.getElementById('wali-container');
        const firstWali = waliContainer.querySelector('.wali-item');
        waliContainer.innerHTML = '';
        waliContainer.appendChild(firstWali);
        firstWali.querySelectorAll('input,select,textarea').forEach(el => el.value = '');
        updateRemoveButtons();
        // Reset previews & box border
        document.querySelectorAll('[id^="prev-"]').forEach(el => { el.innerHTML=''; el.classList.add('hidden'); });
        document.querySelectorAll('[id^="fname-"]').forEach(el => { el.textContent=''; el.classList.add('hidden'); });
        document.querySelectorAll('.border-dashed').forEach(box => {
          box.classList.remove('border-green-400','bg-green-50');
          box.classList.add('border-gray-200','hover:border-primary-300','hover:bg-primary-50');
        });
        goToStep(1);
        Swal.fire({ icon:'success', title:'Berhasil', text:'Formulir telah direset.', timer:2000, showConfirmButton:false });
      }
    });
  }

  // =============================================
  // AUTO SAVE TIAP 3 DETIK
  // =============================================
  setInterval(saveCache, 3000);

  // =============================================
  // FLATPICKR DATEPICKER
  // =============================================
  flatpickr('#tanggal_lahir', {
    dateFormat: 'Y-m-d',
    maxDate: 'today',
    locale: { firstDayOfWeek: 1 },
    allowInput: false,
  });

  // =============================================
  // SUBMIT HANDLER
  // =============================================
  document.getElementById('form-daftar').addEventListener('submit', function(e) {
    if (!validateStep(6)) { e.preventDefault(); return; }
    const btn = document.getElementById('btn-submit');
    btn.disabled = true;
    btn.innerHTML = `<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Mengirim...`;
    // Hapus cache form & file IndexedDB setelah submit berhasil
    try { localStorage.removeItem(CACHE_KEY); } catch(e) {}
    clearAllFilesFromDB().catch(() => {});
  });

  // =============================================
  // HANDLE QUERY PARAM ?sekolah=NamaSekolah
  // =============================================
  function handleSekolahQueryParam() {
    const params = new URLSearchParams(window.location.search);
    const sekolahNama = params.get('sekolah');
    if (!sekolahNama) return false;

    const radio = [...document.querySelectorAll('input[name="sekolah"]')].find(r => {
      return r.dataset.nama && r.dataset.nama.trim().toLowerCase() === sekolahNama.trim().toLowerCase();
    }) || [...document.querySelectorAll('input[name="sekolah"]')].find(r => {
      return r.dataset.nama && r.dataset.nama.toLowerCase().includes(sekolahNama.toLowerCase());
    });

    if (radio) {
      // Uncheck all first
      document.querySelectorAll('input[name="sekolah"]').forEach(r => r.checked = false);
      radio.checked = true;

      // Update visual highlight
      document.querySelectorAll('.sekolah-card-inner').forEach(c => {
        c.classList.remove('border-primary-400', 'bg-primary-50', 'border-primary-300');
        c.classList.add('border-gray-200');
      });
      const cardInner = radio.closest('.sekolah-card')?.querySelector('.sekolah-card-inner');
      if (cardInner) {
        cardInner.classList.add('border-primary-400', 'bg-primary-50');
        cardInner.classList.remove('border-gray-200');
      }

      // Trigger change for jurusan population
      radio.dispatchEvent(new Event('change'));
      return true;
    }
    return false;
  }

  // =============================================
  // INIT
  // =============================================
  // =============================================
  // RESTORE FILE DARI INDEXEDDB SAAT PAGE LOAD
  // =============================================
  async function restoreFilePreviews() {
    const fieldMap = {
      'pas_foto':      { prev: 'prev-pas_foto',    fname: 'fname-pas_foto'    },
      'kk':            { prev: 'prev-kk',           fname: 'fname-kk'          },
      'akta':          { prev: 'prev-akta',         fname: 'fname-akta'        },
      'ijazah':        { prev: 'prev-ijazah',       fname: 'fname-ijazah'      },
      'skhun':         { prev: 'prev-skhun',        fname: 'fname-skhun'       },
      'stl':           { prev: 'prev-stl',          fname: 'fname-stl'         },
      'file_lampiran': { prev: 'prev-lampiran',     fname: 'lampiran-filename' },
    };

    for (const [fieldKey, ids] of Object.entries(fieldMap)) {
      try {
        const file = await getFileFromDB(fieldKey);
        if (!file) continue;

        renderPreview(file, ids.prev, ids.fname);

        // Warnai border box upload
        const inp = document.getElementById('file-' + fieldKey) || document.getElementById(fieldKey);
        if (inp) markBoxSelected(inp);

        // Inject file ke input type=file via DataTransfer supaya bisa di-submit form
        try {
          const dt = new DataTransfer();
          dt.items.add(file);
          if (inp) inp.files = dt.files;
        } catch(e) { /* DataTransfer tidak support semua browser, skip */ }

      } catch(e) { /* file tidak ada di DB */ }
    }
  }

  // =============================================
  // INIT
  // =============================================
  (function initForm() {
    showStep(1);

    const urlSekolah = new URLSearchParams(window.location.search).get('sekolah');

    if (urlSekolah) {
      // Ada ?sekolah= — restore field lain dari cache, skip bagian sekolah
      try {
        const raw = localStorage.getItem(CACHE_KEY);
        if (raw) {
          const d = JSON.parse(raw);
          if (d.asal_sekolah)  document.getElementById('asal_sekolah').value  = d.asal_sekolah;
          if (d.tahun_lulus)   document.getElementById('tahun_lulus').value   = d.tahun_lulus;
          if (d.nomor_ijazah)  document.getElementById('nomor_ijazah').value  = d.nomor_ijazah;
          if (d.nama_lengkap)  document.getElementById('nama_lengkap').value  = d.nama_lengkap;
          if (d.nisn)          document.getElementById('nisn').value          = d.nisn;
          if (d.tempat_lahir)  document.getElementById('tempat_lahir').value  = d.tempat_lahir;
          if (d.tanggal_lahir) document.getElementById('tanggal_lahir').value = d.tanggal_lahir;
          if (d.agama)         document.getElementById('agama').value         = d.agama;
          if (d.alamat)        document.getElementById('alamat').value        = d.alamat;
          if (d.phone)         document.getElementById('phone').value         = d.phone;
          if (d.email)         document.getElementById('email').value         = d.email;
          if (d.ket_jalur)     document.getElementById('ket_jalur').value     = d.ket_jalur;
          if (d.jenis_kelamin) {
            const jkr = document.querySelector(`input[name="jenis_kelamin"][value="${d.jenis_kelamin}"]`);
            if (jkr) jkr.checked = true;
          }
          if (d.jalur) {
            const jr = document.querySelector(`input[name="jalur_pendaftaran"][value="${d.jalur}"]`);
            if (jr) { jr.checked = true; jr.dispatchEvent(new Event('change')); }
          }
          if (d.wali && Array.isArray(d.wali) && d.wali.length > 0) {
            const container = document.getElementById('wali-container');
            const firstItem = container.querySelector('.wali-item');
            if (firstItem && d.wali[0]) {
              const w = d.wali[0];
              const h = firstItem.querySelector('select[name*="[hubungan]"]'); if (h && w.hubungan) h.value = w.hubungan;
              const n = firstItem.querySelector('input[name*="[nama_wali]"]'); if (n && w.nama_wali) n.value = w.nama_wali;
              const p = firstItem.querySelector('input[name*="[pekerjaan]"]'); if (p && w.pekerjaan) p.value = w.pekerjaan;
              const t = firstItem.querySelector('input[name*="[notelp_wali]"]'); if (t && w.notelp_wali) t.value = w.notelp_wali;
              const e = firstItem.querySelector('input[name*="[email]"]'); if (e && w.email) e.value = w.email;
            }
            for (let i = 1; i < d.wali.length; i++) {
              addWali();
              const items = container.querySelectorAll('.wali-item');
              const item = items[items.length - 1];
              const w = d.wali[i];
              const h = item.querySelector('select[name*="[hubungan]"]'); if (h && w.hubungan) h.value = w.hubungan;
              const n = item.querySelector('input[name*="[nama_wali]"]'); if (n && w.nama_wali) n.value = w.nama_wali;
              const p = item.querySelector('input[name*="[pekerjaan]"]'); if (p && w.pekerjaan) p.value = w.pekerjaan;
              const t = item.querySelector('input[name*="[notelp_wali]"]'); if (t && w.notelp_wali) t.value = w.notelp_wali;
              const e = item.querySelector('input[name*="[email]"]'); if (e && w.email) e.value = w.email;
            }
          }
        }
      } catch(e) {}
      handleSekolahQueryParam();
    } else {
      loadCache();
    }

    // Restore file preview dari IndexedDB (async, tidak block UI)
    restoreFilePreviews();

    @if($errors->any())
      @if($errors->has('sekolah') || $errors->has('jurusan') || $errors->has('asal_sekolah'))
        goToStep(1);
      @elseif($errors->has('nama_lengkap') || $errors->has('nisn') || $errors->has('phone') || $errors->has('email'))
        goToStep(2);
      @elseif($errors->has('jalur_pendaftaran') || $errors->has('ket_jalur'))
        goToStep(3);
      @elseif($errors->has('wali'))
        goToStep(4);
      @elseif($errors->has('pas_foto') || $errors->has('kk') || $errors->has('akta'))
        goToStep(5);
      @endif
    @endif
  })();
</script>
@endpush