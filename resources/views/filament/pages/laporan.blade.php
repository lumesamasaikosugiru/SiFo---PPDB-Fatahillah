<x-filament-panels::page>

    {{-- ══════════════════════════════════════════════════════════════════
         FILTER BAR
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 mb-6">
        <div class="flex items-center gap-2 mb-4">
            <x-heroicon-o-funnel class="w-5 h-5 text-primary-500"/>
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Filter Laporan</h3>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            {{-- Dari Tanggal --}}
            <div class="col-span-1">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Dari Tanggal</label>
                <input
                    type="date"
                    wire:model.live="filterDari"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                />
            </div>

            {{-- Sampai Tanggal --}}
            <div class="col-span-1">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sampai Tanggal</label>
                <input
                    type="date"
                    wire:model.live="filterSampai"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                />
            </div>

            {{-- Filter Sekolah (hanya admin yayasan/superadmin) --}}
            @if(auth()->user()->hasAnyRole(['superadmin', 'admin_yayasan']))
            <div class="col-span-1">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sekolah</label>
                <select
                    wire:model.live="filterSekolahId"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                >
                    <option value="">Semua Sekolah</option>
                    @foreach($this->getSekolahOptions() as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Filter Jurusan --}}
            <div class="col-span-1">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Jurusan</label>
                <select
                    wire:model.live="filterJurusanId"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                >
                    <option value="">Semua Jurusan</option>
                    @foreach($this->getJurusanOptions() as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status --}}
            <div class="col-span-1">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select
                    wire:model.live="filterStatus"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                >
                    <option value="">Semua Status</option>
                    @foreach($this->getStatusOptions() as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Reset Filter --}}
            <div class="col-span-1 flex items-end">
                <button
                    wire:click="resetFilters"
                    class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition"
                >
                    <x-heroicon-o-x-mark class="w-4 h-4"/>
                    Reset
                </button>
            </div>
        </div>

        {{-- Info periode aktif --}}
        @if($filterDari || $filterSampai || $filterSekolahId || $filterJurusanId || $filterStatus)
        <div class="mt-3 flex flex-wrap gap-2">
            @if($filterDari)
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-xs rounded-full">
                    Dari: {{ \Carbon\Carbon::parse($filterDari)->format('d/m/Y') }}
                </span>
            @endif
            @if($filterSampai)
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-xs rounded-full">
                    S/D: {{ \Carbon\Carbon::parse($filterSampai)->format('d/m/Y') }}
                </span>
            @endif
            @if($filterSekolahId)
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs rounded-full">
                    Sekolah: {{ \App\Models\Sekolah::find($filterSekolahId)?->nama_sekolah ?? '-' }}
                </span>
            @endif
            @if($filterJurusanId)
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs rounded-full">
                    Jurusan: {{ \App\Models\Jurusan::find($filterJurusanId)?->nama_jurusan ?? '-' }}
                </span>
            @endif
            @if($filterStatus)
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 text-xs rounded-full">
                    Status: {{ $this->getStatusLabel($filterStatus) }}
                </span>
            @endif
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         SUMMARY CARDS
    ══════════════════════════════════════════════════════════════════ --}}
    @php $summary = $this->getSummaryData(); @endphp

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-4 mb-6">
        {{-- Total --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 col-span-1">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Total Pendaftar</p>
            <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $summary['total'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Semua status</p>
        </div>

        {{-- Diproses --}}
        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl shadow-sm border border-amber-200 dark:border-amber-700 p-4">
            <p class="text-xs font-medium text-amber-600 dark:text-amber-400 mb-1">Diproses</p>
            <p class="text-3xl font-bold text-amber-700 dark:text-amber-300">{{ $summary['diproses'] }}</p>
            <p class="text-xs text-amber-500 mt-1">Dalam verifikasi</p>
        </div>

        {{-- Diterima --}}
        <div class="bg-green-50 dark:bg-green-900/20 rounded-xl shadow-sm border border-green-200 dark:border-green-700 p-4">
            <p class="text-xs font-medium text-green-600 dark:text-green-400 mb-1">Diterima</p>
            <p class="text-3xl font-bold text-green-700 dark:text-green-300">{{ $summary['diterima'] }}</p>
            <p class="text-xs text-green-500 mt-1">Diterima admin</p>
        </div>

        {{-- Ditolak --}}
        <div class="bg-red-50 dark:bg-red-900/20 rounded-xl shadow-sm border border-red-200 dark:border-red-700 p-4">
            <p class="text-xs font-medium text-red-600 dark:text-red-400 mb-1">Ditolak</p>
            <p class="text-3xl font-bold text-red-700 dark:text-red-300">{{ $summary['ditolak'] }}</p>
            <p class="text-xs text-red-500 mt-1">Tidak lolos seleksi</p>
        </div>

        {{-- Sudah Bayar --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl shadow-sm border border-blue-200 dark:border-blue-700 p-4">
            <p class="text-xs font-medium text-blue-600 dark:text-blue-400 mb-1">Sudah Bayar</p>
            <p class="text-3xl font-bold text-blue-700 dark:text-blue-300">{{ $summary['lunas'] }}</p>
            <p class="text-xs text-blue-500 mt-1">Pembayaran lunas</p>
        </div>

        {{-- Belum Bayar --}}
        <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl shadow-sm border border-orange-200 dark:border-orange-700 p-4">
            <p class="text-xs font-medium text-orange-600 dark:text-orange-400 mb-1">Belum Bayar</p>
            <p class="text-3xl font-bold text-orange-700 dark:text-orange-300">{{ $summary['blmBayar'] }}</p>
            <p class="text-xs text-orange-500 mt-1">Menunggu pembayaran</p>
        </div>

        {{-- Selesai --}}
        <div class="bg-teal-50 dark:bg-teal-900/20 rounded-xl shadow-sm border border-teal-200 dark:border-teal-700 p-4">
            <p class="text-xs font-medium text-teal-600 dark:text-teal-400 mb-1">Selesai</p>
            <p class="text-3xl font-bold text-teal-700 dark:text-teal-300">{{ $summary['selesai'] }}</p>
            <p class="text-xs text-teal-500 mt-1">Proses selesai</p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         TABEL REKAP
    ══════════════════════════════════════════════════════════════════ --}}
    @php $records = $this->getTableData(); @endphp

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        {{-- Tabel header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-primary-500"/>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Rekap Keseluruhan Pendaftar
                </h3>
                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300">
                    {{ $records->count() }} data
                </span>
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500">
                Dicetak: {{ now()->format('d/m/Y H:i') }}
            </p>
        </div>

        {{-- Scrollable table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">
                        <th class="px-4 py-3 w-10">#</th>
                        <th class="px-4 py-3">Kode Registrasi</th>
                        <th class="px-4 py-3">Nama Calon Siswa</th>
                        <th class="px-4 py-3">NISN</th>
                        <th class="px-4 py-3">Sekolah Tujuan</th>
                        <th class="px-4 py-3">Jurusan</th>
                        <th class="px-4 py-3">Jalur Daftar</th>
                        <th class="px-4 py-3">Tgl Submit</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Pembayaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($records as $i => $r)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors {{ $i % 2 === 0 ? '' : 'bg-gray-50/50 dark:bg-gray-800/20' }}">
                        <td class="px-4 py-3 text-gray-400 font-mono text-xs">{{ $i + 1 }}</td>

                        <td class="px-4 py-3">
                            <span class="font-mono text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded">
                                {{ $r->kode_regis ?? '-' }}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800 dark:text-gray-200">{{ $r->siswa?->nama_siswa ?? '-' }}</p>
                            <p class="text-xs text-gray-400">{{ $r->siswa?->asal_sekolah ?? '' }}</p>
                        </td>

                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs font-mono">
                            {{ $r->siswa?->nisn ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-xs">
                            {{ $r->sekolah?->nama_sekolah ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-xs">
                            {{ $r->jurusan?->nama_jurusan ?? '-' }}
                        </td>

                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                                {{ ucfirst($r->jalur_pendaftaran ?? '-') }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                            {{ $r->tanggal_submit?->format('d/m/Y') ?? '-' }}
                        </td>

                        {{-- Status Pendaftaran --}}
                        <td class="px-4 py-3">
                            @php
                                $statusColor = match($r->status) {
                                    'diterima'         => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                                    'ditolak'          => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                    'diverifikasi'     => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                    'pembayaran_lunas' => 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300',
                                    'selesai'          => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                    'menunggu_pembayaran', 'pembayaran_diproses' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                                    default            => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                                };
                                $statusLabel = $this->getStatusLabel($r->status ?? '');
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusColor }}">
                                {{ $statusLabel }}
                            </span>
                        </td>

                        {{-- Status Pembayaran --}}
                        <td class="px-4 py-3">
                            @php
                                $latestBayar = $r->pembayarans->sortByDesc('created_at')->first();
                                $bayarStatus = $latestBayar?->status_pembayaran;
                                $bayarLabel  = match($bayarStatus) {
                                    'sukses'              => 'Lunas',
                                    'menunggu_verifikasi' => 'Menunggu Verif',
                                    'pending'             => 'Pending',
                                    'gagal'               => 'Gagal',
                                    'kadaluarsa'          => 'Kadaluarsa',
                                    default               => 'Belum Bayar',
                                };
                                $bayarColor = match($bayarStatus) {
                                    'sukses'              => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                                    'menunggu_verifikasi' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                    'pending'             => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                                    'gagal', 'kadaluarsa' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                    default               => 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $bayarColor }}">
                                {{ $bayarLabel }}
                            </span>
                            @if($latestBayar?->nominal)
                            <p class="text-xs text-gray-400 mt-0.5">
                                Rp {{ number_format($latestBayar->nominal, 0, ',', '.') }}
                            </p>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center gap-2 text-gray-400">
                                <x-heroicon-o-document-magnifying-glass class="w-10 h-10"/>
                                <p class="text-sm font-medium">Tidak ada data pendaftar</p>
                                <p class="text-xs">Coba ubah filter pencarian</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer info --}}
        @if($records->count() > 0)
        <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
            <p class="text-xs text-gray-400">
                Menampilkan <strong class="text-gray-600 dark:text-gray-300">{{ $records->count() }}</strong> data pendaftar
                {{ $filterDari || $filterSampai ? '· Periode: ' . ($filterDari ? \Carbon\Carbon::parse($filterDari)->format('d/m/Y') : '—') . ' s/d ' . ($filterSampai ? \Carbon\Carbon::parse($filterSampai)->format('d/m/Y') : 'sekarang') : '' }}
            </p>
        </div>
        @endif
    </div>

</x-filament-panels::page>
