<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Siswa;
use App\Models\WaliSiswa;
use App\Models\Dokumen;
use App\Models\Sekolah;
use App\Models\Jurusan;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PendaftaranController extends Controller
{
    public function create()
    {
        $sekolahs = Sekolah::with(['jurusans' => function ($q) {
            $q->where('is_active', 1)->orderBy('nama_jurusan');
        }])->orderBy('nama_sekolah')->get();

        return view('daftar', compact('sekolahs'));
    }

    public function store(Request $request)
    {
        $sekolah = Sekolah::find($request->sekolah);
        $isSMK   = $sekolah && strtoupper($sekolah->tingkatan) === 'SMK';

        $validated = $request->validate([
            // ===== SEKOLAH =====
            'sekolah'           => ['required', 'exists:sekolahs,id'],
            'jurusan'           => [
                $isSMK ? 'required' : 'nullable',
                'nullable',
                'exists:jurusans,id',
            ],

            // ===== DATA SISWA =====
            'nama_lengkap'      => 'required|string|max:50',
            'nisn'              => 'required|digits:10',
            // Form kirim 'Laki-Laki'/'Perempuan', kita map ke enum DB
            'jenis_kelamin'     => ['required', Rule::in(['Laki-Laki', 'Perempuan'])],
            'tempat_lahir'      => 'required|string|max:50',
            'tanggal_lahir'     => 'required|date',
            // Form kirim 'Islam','Kristen', dll — kita map ke lowercase untuk DB
            'agama'             => ['required', Rule::in(['Islam','Kristen','Katolik','Hindu','Budha','Konghucu'])],
            'alamat'            => 'required|string',
            'phone'             => 'required|regex:/^[0-9]+$/|max:15',
            'email'             => 'required|email|max:50',

            // ===== SEKOLAH ASAL =====
            'asal_sekolah'      => 'required|string|max:50',
            'tahun_lulus'       => ['required', 'digits:4', 'integer', 'min:' . (date('Y') - 10), 'max:' . date('Y')],
            'nomor_ijazah'      => 'required|string|max:255',

            // ===== JALUR =====
            // Form kirim 'Regular','Prestasi','Afirmasi','Pindahan'
            // DB enum: reguler,prestasi,afirmasi,pindahan
            'jalur_pendaftaran' => ['required', Rule::in(['Regular', 'Prestasi', 'Afirmasi', 'Pindahan'])],
            'ket_jalur'         => [
                Rule::requiredIf(fn () => in_array($request->jalur_pendaftaran, ['Prestasi', 'Afirmasi', 'Pindahan'])),
                'nullable', 'string', 'max:1000',
            ],
            'file_lampiran'     => [
                Rule::requiredIf(fn () => in_array($request->jalur_pendaftaran, ['Prestasi', 'Afirmasi'])),
                'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:20480',
            ],

            // ===== DATA WALI =====
            // Form kirim hubungan: bapak/ibu/saudara_kandung/saudara_keluarga
            // DB enum: orang_tua,saudara_kandung,saudara_keluarga
            // bapak & ibu = orang_tua
            'wali'                  => 'required|array|min:1',
            'wali.*.nama_wali'      => 'required|string|max:50',
            'wali.*.hubungan'       => ['required', Rule::in(['bapak', 'ibu', 'saudara_kandung', 'saudara_keluarga'])],
            'wali.*.pekerjaan'      => 'required|string|max:30',
            'wali.*.notelp_wali'    => 'nullable|regex:/^[0-9]+$/|max:15',
            'wali.*.email'          => 'nullable|email|max:100',

            // ===== DOKUMEN =====
            'pas_foto'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480',
            'kk'        => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480',
            'akta'      => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480',
            'ijazah'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
            'skhun'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
            'stl'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ], [
            'wali.required'             => 'Data orang tua/wali wajib diisi minimal 1.',
            'wali.min'                  => 'Data orang tua/wali wajib diisi minimal 1.',
            'wali.*.nama_wali.required' => 'Nama wali wajib diisi.',
            'wali.*.hubungan.required'  => 'Hubungan wali wajib dipilih.',
            'wali.*.pekerjaan.required' => 'Pekerjaan wali wajib diisi.',
            'pas_foto.required'         => 'Pas foto wajib diupload.',
            'kk.required'               => 'Kartu Keluarga wajib diupload.',
            'akta.required'             => 'Akta Kelahiran wajib diupload.',
            'phone.regex'               => 'Nomor HP hanya boleh angka.',
            'nisn.digits'               => 'NISN harus 10 digit angka.',
            'tahun_lulus.min'           => 'Tahun lulus tidak valid.',
            'tahun_lulus.max'           => 'Tahun lulus tidak valid.',
        ]);

        DB::beginTransaction();
        try {
            // ===== TAHUN AKADEMIK AKTIF =====
            $tahunAkademik = TahunAkademik::where('is_active', 1)->first()
                ?? TahunAkademik::latest()->first();

            // ===== GENERATE KODE REGISTRASI =====
            $kodeRegis = $this->generateKodeRegistrasi();

            // ===== MAP JALUR: form value → DB enum =====
            $jalurMap = [
                'Regular'  => 'reguler',
                'Prestasi' => 'prestasi',
                'Afirmasi' => 'afirmasi',
                'Pindahan' => 'pindahan',
            ];
            $jalurDb = $jalurMap[$validated['jalur_pendaftaran']] ?? 'reguler';

            // ===== INSERT PENDAFTARAN =====
            $pendaftaran = Pendaftaran::create([
                'kode_regis'        => $kodeRegis,
                'tahun_akademik_id' => $tahunAkademik?->id,
                'sekolah_id'        => $validated['sekolah'],
                'jurusan_id'        => $validated['jurusan'] ?? null,
                'jalur_pendaftaran' => $jalurDb,
                'status'            => 'diproses',
                'tanggal_submit'    => now()->toDateString(),
                'dibuat_oleh'       => 'publik',
            ]);

            // ===== MAP JK: form → DB enum =====
            $jkMap = [
                'Laki-Laki' => 'laki_laki',
                'Perempuan'  => 'perempuan',
            ];

            // ===== MAP AGAMA: form → DB enum (lowercase) =====
            $agamaMap = [
                'Islam'    => 'islam',
                'Kristen'  => 'protestan',
                'Katolik'  => 'katolik',
                'Hindu'    => 'hindu',
                'Budha'    => 'budha',
                'Konghucu' => 'khonghucu',
            ];

            // ===== INSERT SISWA =====
            $siswa = Siswa::create([
                'pendaftaran_id' => $pendaftaran->id,
                'nisn'           => $validated['nisn'],
                'nama_siswa'     => $validated['nama_lengkap'],
                'jk'             => $jkMap[$validated['jenis_kelamin']] ?? 'laki_laki',
                'phone'          => $validated['phone'],
                'email'          => $validated['email'],
                'agama'          => $agamaMap[$validated['agama']] ?? 'islam',
                'tempat_lahir'   => $validated['tempat_lahir'],
                'tanggal_lahir'  => $validated['tanggal_lahir'],
                'asal_sekolah'   => $validated['asal_sekolah'],
                'tahun_lulus'    => $validated['tahun_lulus'],
                'nomor_ijazah'   => $validated['nomor_ijazah'],
            ]);

            // ===== MAP HUBUNGAN WALI: bapak/ibu → orang_tua =====
            $hubunganMap = [
                'bapak'            => 'orang_tua',
                'ibu'              => 'orang_tua',
                'saudara_kandung'  => 'saudara_kandung',
                'saudara_keluarga' => 'saudara_keluarga',
            ];

            // ===== INSERT WALI SISWA =====
            $emailWaliList = [];
            foreach ($validated['wali'] as $waliData) {
                WaliSiswa::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'nama_wali'      => $waliData['nama_wali'],
                    'hubungan'       => $hubunganMap[$waliData['hubungan']] ?? 'orang_tua',
                    'pekerjaan'      => $waliData['pekerjaan'],
                    'notelp_wali'    => $waliData['notelp_wali'] ?? '',
                    'email'          => substr($waliData['email'] ?? '', 0, 15), // varchar(15) di DB
                ]);
                if (!empty($waliData['email'])) {
                    $emailWaliList[] = $waliData['email'];
                }
            }

            // ===== UPLOAD DOKUMEN =====
            $dokumenFields = ['pas_foto', 'kk', 'akta', 'ijazah', 'skhun', 'stl'];
            foreach ($dokumenFields as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('ppdb/' . $kodeRegis, 'public');
                    Dokumen::create([
                        'pendaftaran_id' => $pendaftaran->id,
                        'tipe_dokumen'   => $field,
                        'file_path'      => $path,
                    ]);
                }
            }

            // Upload lampiran jalur (Prestasi/Afirmasi)
            if ($request->hasFile('file_lampiran')) {
                $path = $request->file('file_lampiran')->store('ppdb/' . $kodeRegis . '/lampiran', 'public');
                Dokumen::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'tipe_dokumen'   => 'lampiran_jalur',
                    'file_path'      => $path,
                ]);
            }

            DB::commit();

            // ===== KIRIM EMAIL =====
            $emailTargets = array_filter(array_unique(array_merge(
                [$validated['email']],
                $emailWaliList
            )));

            try {
                set_time_limit(60);
                $this->kirimEmailPendaftaran($pendaftaran, $siswa, $emailTargets);
            } catch (\Throwable $e) {
                Log::warning('Email pendaftaran gagal (timeout/error): ' . $e->getMessage());
            }

            // ===== REDIRECT KE HALAMAN FINISH =====
            return redirect()->route('pendaftaran.finish', ['kode' => $kodeRegis]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Pendaftaran error: ' . $e->getMessage() . ' | ' . $e->getTraceAsString());
            return back()
                ->withErrors(['error' => 'Gagal menyimpan pendaftaran: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // =============================================
    // HALAMAN FINISH SETELAH SUBMIT
    // =============================================
    public function finish(Request $request)
    {
        $kode = $request->query('kode');
        if (!$kode) return redirect()->route('home');

        $pendaftaran = Pendaftaran::with(['siswa', 'sekolah', 'jurusan', 'waliSiswas'])
            ->where('kode_regis', $kode)
            ->first();

        if (!$pendaftaran) return redirect()->route('home');

        return view('pendaftaran-selesai', compact('pendaftaran'));
    }

    // =============================================
    // GENERATE KODE REGISTRASI
    // Format: PPDB26-XXXXXXXX (8 karakter random huruf+angka)
    // Contoh: PPDB26-AB3XY7KZ
    // =============================================
    public function generateKodeRegistrasi(): string
    {
        $year   = date('y'); // 2 digit tahun: 26, 27, dst
        $prefix = "PPDB{$year}-";

        // Generate unik — retry jika collision (sangat jarang terjadi)
        do {
            $kode = $prefix . strtoupper(\Illuminate\Support\Str::random(8));
        } while (Pendaftaran::where('kode_regis', $kode)->exists());

        return $kode;
    }

    // =============================================
    // KIRIM EMAIL NOTIFIKASI
    // =============================================
    private function kirimEmailPendaftaran(Pendaftaran $pendaftaran, Siswa $siswa, array $emailTargets): void
    {
        if (empty($emailTargets)) return;

        $sekolahNama = $pendaftaran->sekolah->nama_sekolah ?? '-';
        $jurusanNama = $pendaftaran->jurusan->nama_jurusan ?? 'Tidak Ada (SMP)';
        $kodeRegis   = $pendaftaran->kode_regis;
        $namaSiswa   = $siswa->nama_siswa;
        $jalur       = ucfirst($pendaftaran->jalur_pendaftaran);
        $tanggal     = \Carbon\Carbon::parse($pendaftaran->tanggal_submit)->translatedFormat('d F Y');
        $statusUrl   = route('status.index', [
            'kode' => $kodeRegis
        ]);
        $subject     = "Konfirmasi Pendaftaran PPDB - {$kodeRegis}";

        $body = "<!DOCTYPE html>
        <html lang='id'>
        <head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1'>
        <style>
            body{font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:0}
            .wrap{max-width:600px;margin:30px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.1)}
            .hdr{background:linear-gradient(135deg,#0f4c3a,#1a6b55);color:#fff;padding:36px 30px;text-align:center}
            .hdr h1{margin:0;font-size:22px}.hdr p{margin:6px 0 0;opacity:.85;font-size:14px}
            .badge{display:inline-block;background:rgba(255,255,255,.2);border-radius:20px;padding:4px 14px;font-size:13px;margin-top:10px}
            .body{padding:28px 30px}
            .kode-box{background:#0f4c3a;color:#fff;border-radius:10px;padding:18px;text-align:center;margin:20px 0}
            .kode{font-size:30px;font-weight:700;letter-spacing:4px;font-family:monospace}
            .kode small{display:block;margin-top:4px;opacity:.7;font-size:12px}
            .info{background:#f0faf8;border-left:4px solid #3d9080;border-radius:8px;padding:18px;margin:16px 0}
            .row{display:flex;margin-bottom:8px;font-size:13px}
            .lbl{color:#666;width:140px;flex-shrink:0}
            .val{font-weight:600;color:#111}
            .steps{margin:16px 0}
            .step{display:table;width:100%;margin-bottom:12px}
            .snum{display:table-cell;background:#3d9080;color:#fff;border-radius:50%;width:26px;min-width:26px;font-size:12px;font-weight:700;text-align:center;vertical-align:middle;padding:4px 6px;}
            .stxt{display:table-cell;vertical-align:top;padding-left:12px;padding-top:3px;}
            .btn{display:block;width:fit-content;margin:20px auto;background:#3d9080;color:#fff;text-decoration:none;padding:13px 30px;border-radius:8px;font-weight:700;font-size:14px}
            .ftr{background:#f9fafb;border-top:1px solid #eee;padding:18px 30px;text-align:center;color:#999;font-size:12px}
        </style></head>
        <body><div class='wrap'>
            <div class='hdr'>
                <h1>🎓 Yayasan Fatahillah</h1>
                <p>PPDB Online — Tahun Ajaran 2026/2027</p>
                <span class='badge'>✅ Pendaftaran Berhasil</span>
            </div>
            <div class='body'>
                <p>Assalamualaikum, <strong>{$namaSiswa}</strong>.</p>
                <p>Pendaftaran PPDB Anda telah berhasil. Simpan nomor berikut untuk cek status:</p>
                <div class='kode-box'>
                    <div class='kode'>{$kodeRegis}</div>
                    <small>Nomor Pendaftaran Anda</small>
                </div>
                <div class='info'>
                    <div class='row'><span class='lbl'>Nama Siswa</span><span class='val'>{$namaSiswa}</span></div>
                    <div class='row'><span class='lbl'>Sekolah Tujuan</span><span class='val'>{$sekolahNama}</span></div>
                    <div class='row'><span class='lbl'>Jurusan</span><span class='val'>{$jurusanNama}</span></div>
                    <div class='row'><span class='lbl'>Jalur</span><span class='val'>{$jalur}</span></div>
                    <div class='row'><span class='lbl'>Tanggal Daftar</span><span class='val'>{$tanggal}</span></div>
                    <div class='row'><span class='lbl'>Status</span><span class='val'>⏳ Menunggu Verifikasi</span></div>
                </div>
                <h3 style='font-size:14px;color:#111;margin-bottom:10px'>Langkah Selanjutnya:</h3>
                <div class='steps'>
                    <div class='step'><div class='snum'>1</div><div class='stxt'><strong>Tunggu Verifikasi</strong><br><small style='color:#666'>Panitia akan memverifikasi data dan dokumen Anda dalam beberapa hari kerja.</small></div></div>
                    <div class='step'><div class='snum'>2</div><div class='stxt'><strong>Pantau Status</strong><br><small style='color:#666'>Gunakan nomor pendaftaran di atas untuk cek status secara berkala.</small></div></div>
                    <div class='step'><div class='snum'>3</div><div class='stxt'><strong>Pengumuman</strong><br><small style='color:#666'>Hasil seleksi diumumkan sesuai jadwal PPDB 2026/2027.</small></div></div>
                    <div class='step'><div class='snum'>4</div><div class='stxt'><strong>Pembayaran</strong><br><small style='color:#666'>Jika diterima, segera lakukan pembayaran uang pendaftaran melalui link yang tersedia.</small></div></div>
                </div>
                <a href='{$statusUrl}' class='btn' style='color:white !important;'>🔍 Cek Status Pendaftaran</a>
                <p style='color:#aaa;font-size:11px;text-align:center;margin-top:16px'>Jika Anda tidak merasa mendaftar, abaikan email ini.<br>Hubungi kami: <a href='mailto:info@fatahillah.sch.id'>info@fatahillah.sch.id</a></p>
            </div>
            <div class='ftr'>
                <p>&copy; " . date('Y') . " Yayasan Fatahillah. Jl. Fatahillah No.1, Cilegon, Banten.</p>
                <p>Email ini dikirim otomatis, mohon jangan balas.</p>
            </div>
        </div></body></html>";

        foreach ($emailTargets as $email) {
            Mail::html($body, function ($msg) use ($email, $subject) {
                $msg->to($email)
                    ->subject($subject)
                    ->from(
                        config('mail.from.address', 'ppdb@fatahillah.sch.id'),
                        config('mail.from.name', 'PPDB Yayasan Fatahillah')
                    );
            });
        }
    }
}