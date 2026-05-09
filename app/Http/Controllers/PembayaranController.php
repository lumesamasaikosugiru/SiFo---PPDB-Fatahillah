<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\MetodePembayaran;
use App\Models\Siswa;
use App\Models\WaliSiswa;
use App\Models\AdminSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class PembayaranController extends Controller
{
    const NOMINAL = 200000;

    const WA_ADMIN = [
        1 => '6208993388681',
        2 => '6208993388681',
        3 => '6208993388681',
        4 => '6208993388681',
    ];

    // =============================================
    // HELPER: Apakah APP_URL adalah localhost?
    // =============================================
    /**
     * Ambil daftar email admin dari ENV (ADMIN_EMAIL).
     * Support multiple email dipisah koma.
     * Contoh .env: ADMIN_EMAIL=admin1@mail.com,admin2@mail.com
     */
    private function getAdminEmails(): array
    {
        $raw = config('mail.admin_email', env('ADMIN_EMAIL', ''));
        if (empty($raw)) return [];
        return array_filter(array_map('trim', explode(',', $raw)));
    }


    private function isLocalhost(): bool
    {
        $appUrl = config('app.url', '');
        return str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1');
    }

    // =============================================
    // 1. HALAMAN UTAMA — input kode pendaftaran
    // =============================================
    public function index()
    {
        return view('pembayaran.index');
    }

    // =============================================
    // 2. CEK KODE — validasi & tampilkan form bayar
    // =============================================
    public function cek(Request $request)
    {
        $request->validate([
            'kode_registrasi' => 'required|string|max:20',
        ], [
            'kode_registrasi.required' => 'Nomor pendaftaran wajib diisi.',
        ]);

        $kode = strtoupper(trim($request->kode_registrasi));

        $pendaftaran = Pendaftaran::with([
            'siswa', 'sekolah', 'jurusan',
            'pembayarans.metodePembayaran',
        ])->where('kode_regis', $kode)->first();

        if (!$pendaftaran) {
            return back()->withErrors([
                'kode_registrasi' => 'Nomor pendaftaran tidak ditemukan.',
            ])->withInput();
        }

        // Ambil semua metode aktif (termasuk 'otomatis' = Midtrans)
        $metodePembayaran = MetodePembayaran::where('is_active', 1)
            ->orderBy('id')
            ->get();

        $waAdmin = self::WA_ADMIN[$pendaftaran->sekolah_id] ?? '6208993388681';

        // Cek pembayaran pending (sedang menunggu snap midtrans)
        $pembayaranPending = $pendaftaran->pembayarans()
            ->where('status_pembayaran', 'pending')
            ->latest()->first();

        // Cek pembayaran aktif (sudah submit manual / sudah sukses)
        $pembayaranAktif = $pendaftaran->pembayarans()
            ->whereIn('status_pembayaran', ['menunggu_verifikasi', 'sukses'])
            ->latest()->first();

        $isLocalhost = $this->isLocalhost();

        return view('pembayaran.form', compact(
            'pendaftaran', 'metodePembayaran', 'waAdmin',
            'pembayaranAktif', 'pembayaranPending', 'isLocalhost'
        ));
    }

    // =============================================
    // 3A. REQUEST SNAP TOKEN ke riplabs
    // =============================================
    public function requestSnapToken(Request $request)
    {
        $request->validate([
            'kode_regis' => 'required|string|exists:pendaftarans,kode_regis',
        ]);

        // Cek apakah localhost — kalau iya, midtrans online tetap bisa,
        // tapi redirect balik ke localhost
        // (snap midtrans bisa muncul dari localhost, hanya notification URL yang harus online)

        $pendaftaran = Pendaftaran::with(['siswa', 'sekolah', 'waliSiswas', 'pembayarans'])
            ->where('kode_regis', $request->kode_regis)
            ->firstOrFail();

        if (!in_array($pendaftaran->status, ['diterima', 'menunggu_pembayaran'])) {
            return response()->json([
                'status'  => false,
                'message' => 'Status pendaftaran tidak memenuhi syarat untuk pembayaran.'
            ], 422);
        }

        // Cek apakah sudah ada pembayaran sukses
        $sudahSukses = $pendaftaran->pembayarans()
            ->where('status_pembayaran', 'sukses')
            ->exists();
        if ($sudahSukses) {
            return response()->json([
                'status'  => false,
                'message' => 'Pendaftaran ini sudah lunas.'
            ], 422);
        }

        // Kalau ada pending lama, coba pakai snap_token yang sama dulu
        $pembayaranPending = $pendaftaran->pembayarans()
            ->where('status_pembayaran', 'pending')
            ->whereNotNull('snap_token')
            ->latest()
            ->first();

        if ($pembayaranPending && $pembayaranPending->snap_token) {
            return response()->json([
                'status'     => true,
                'snap_token' => $pembayaranPending->snap_token,
                'order_id'   => $pembayaranPending->order_id,
            ]);
        }

        // Generate order_id baru
        $prefix   = config('midtrans.order_prefix', 'PPDBATIKA');
        $orderId  = $prefix . strtoupper(substr(md5(uniqid($pendaftaran->kode_regis, true)), 0, 12));
        $namaSiswa = $pendaftaran->siswa->nama_siswa ?? $pendaftaran->kode_regis;
        $email    = $pendaftaran->siswa->email ?? 'noemail@ppdb.sch.id';
        $phone    = $pendaftaran->siswa->phone ?? '0';
        
        // Request snap token ke restapi.riplabs.co.id
        $riplabsKey = config('midtrans.riplabs_key', 'a9s8d7bas98d7981273xbasduky8b71o247bai8f');
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => config('midtrans.riplabs_snaptoken_url', 'https://restapi.riplabs.co.id/snaptokenppdbatika/getsnaptoken'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => [
                'key'         => $riplabsKey,
                'order_id'    => $orderId,
                'total_harga' => (string) self::NOMINAL,
                'nama'        => $namaSiswa,
                'email'       => $email,
                'notelp'      => $phone,
                'namaproduk'  => 'Uang Pendaftaran PPDB - ' . $pendaftaran->kode_regis,
            ],
            CURLOPT_TIMEOUT        => 30,
        ]);
        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        Log::info('Riplabs snap token request', [
            'order_id'  => $orderId,
            'http_code' => $httpCode,
            'curl_err'  => $curlError ?: null,
            'response'  => $response,
        ]);

        if ($curlError || !$response || $httpCode !== 200) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal terhubung ke server pembayaran. Error: ' . ($curlError ?: "HTTP {$httpCode}"),
            ], 500);
        }

        $data = json_decode($response, true);
        $snapToken = $data['snaptoken'] ?? $data['snap_token'] ?? null;
        $isSuccess = !empty($snapToken) &&
            (($data['status'] ?? null) === true || ($data['error'] ?? 1) === 0 || ($data['error'] ?? '1') === '0');

        if (!$isSuccess || !$snapToken) {
            $errMsg = $data['message'] ?? $data['msg'] ?? $data['error_message'] ?? 'Gagal mendapatkan token pembayaran.';
            Log::error('Riplabs snap token gagal', ['data' => $data]);
            return response()->json([
                'status'  => false,
                'message' => is_string($errMsg) ? $errMsg : 'Gagal mendapatkan token pembayaran.',
            ], 500);
        }

        DB::beginTransaction();
        try {
            // Buat record pembayaran baru dengan status pending
            $metodeMidtrans = MetodePembayaran::where('nama_metode', 'otomatis')->first();
            Pembayaran::create([
                'metode_pembayaran_id' => $metodeMidtrans?->id,
                'pendaftaran_id'       => $pendaftaran->id,
                'nominal'              => self::NOMINAL,
                'order_id'             => $orderId,
                'snap_token'           => $snapToken,
                'status_pembayaran'    => 'pending',
                'tanggal_pembayaran'   => now()->toDateString(),
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal simpan pembayaran pending: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menyimpan data pembayaran.'
            ], 500);
        }

        return response()->json([
            'status'     => true,
            'snap_token' => $snapToken,
            'order_id'   => $orderId,
        ]);
    }

    // =============================================
    // 3B. CALLBACK dari riplabs setelah payment
    //     POST /bayar/onprogressmidtrans
    // =============================================
    public function onProgressMidtrans(Request $request)
    {
        // Validasi key dari riplabs
        $key = $request->input('key', '');
        if ($key !== config('midtrans.callback_key')) {
            Log::warning('Midtrans callback: invalid key', ['key' => $key]);
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $transactionStatus = $request->input('transaction_status', '');
        $paymentType       = $request->input('payment_type', '');
        $orderId           = $request->input('order_id', '');

        if (!$transactionStatus || !$paymentType || !$orderId) {
            return response()->json(['status' => false, 'message' => 'Parameter tidak lengkap'], 422);
        }

        $pembayaran = Pembayaran::with(['pendaftaran.siswa', 'pendaftaran.sekolah', 'pendaftaran.waliSiswas'])
            ->where('order_id', $orderId)
            ->first();

        if (!$pembayaran) {
            Log::warning('Midtrans callback: order not found', ['order_id' => $orderId]);
            return response()->json(['status' => false, 'message' => 'Order tidak ditemukan'], 404);
        }

        // Hanya proses jika masih pending
        if ($pembayaran->status_pembayaran !== 'pending') {
            return response()->json(['status' => true, 'message' => 'Sudah diproses sebelumnya']);
        }

        switch ($transactionStatus) {

            // ── SUKSES: settlement (transfer/VA sudah masuk) atau capture (CC) ──
            case 'settlement':
            case 'capture':
                DB::beginTransaction();
                try {
                    $pembayaran->update([
                        'status_pembayaran'    => 'sukses',
                        'metode_pembayaran_id' => $this->resolveMetodeId($paymentType),
                        'verifikasi_tanggal'   => now(),
                    ]);
                    // pendaftarans.status → pembayaran_lunas (dikonfirmasi otomatis Midtrans)
                    $pembayaran->pendaftaran->update(['status' => 'pembayaran_lunas']);
                    DB::commit();
                    Log::info('Midtrans sukses', ['order_id' => $orderId, 'payment_type' => $paymentType]);

                    // Kirim email konfirmasi + PDF
                    try {
                        set_time_limit(120);
                        $this->kirimEmailPembayaranSukses($pembayaran->pendaftaran, $pembayaran, $paymentType);
                    } catch (\Throwable $e) {
                        Log::warning('Email sukses midtrans gagal: ' . $e->getMessage());
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Callback midtrans settlement error: ' . $e->getMessage());
                    return response()->json(['status' => false, 'message' => 'DB error'], 500);
                }
                return response()->json(['status' => true, 'message' => 'Pembayaran berhasil dikonfirmasi']);

            // ── ON-PROGRESS: user sudah pilih metode tapi belum selesai bayar ──
            // Contoh: VA sudah dibuat, transfer belum masuk; QRIS sudah scan belum bayar
            case 'pending':
                DB::beginTransaction();
                try {
                    $pembayaran->update([
                        'status_pembayaran'    => 'menunggu_verifikasi',
                        'metode_pembayaran_id' => $this->resolveMetodeId($paymentType),
                    ]);
                    // pendaftarans.status → pembayaran_diproses
                    $pembayaran->pendaftaran->update(['status' => 'pembayaran_diproses']);
                    DB::commit();
                    Log::info('Midtrans pending→menunggu_verifikasi', ['order_id' => $orderId, 'payment_type' => $paymentType]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Callback midtrans pending error: ' . $e->getMessage());
                }
                return response()->json(['status' => true, 'message' => 'Pembayaran on-progress, menunggu konfirmasi']);

            // ── DITOLAK / DIBATALKAN ──
            case 'deny':
            case 'cancel':
                DB::beginTransaction();
                try {
                    $pembayaran->update(['status_pembayaran' => 'gagal']);
                    // Kembalikan status ke menunggu_pembayaran agar bisa coba lagi
                    if ($pembayaran->pendaftaran->status === 'pembayaran_diproses') {
                        $pembayaran->pendaftaran->update(['status' => 'menunggu_pembayaran']);
                    }
                    DB::commit();
                    Log::info('Midtrans gagal/cancel', ['order_id' => $orderId, 'transaction_status' => $transactionStatus]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Callback midtrans cancel error: ' . $e->getMessage());
                }
                return response()->json(['status' => true, 'message' => 'Dibatalkan/ditolak']);

            // ── KADALUARSA ──
            case 'expire':
                DB::beginTransaction();
                try {
                    $pembayaran->update(['status_pembayaran' => 'kadaluarsa']);
                    // Kembalikan status ke menunggu_pembayaran agar bisa coba lagi
                    if (in_array($pembayaran->pendaftaran->status, ['pembayaran_diproses', 'menunggu_pembayaran'])) {
                        $pembayaran->pendaftaran->update(['status' => 'menunggu_pembayaran']);
                    }
                    DB::commit();
                    Log::info('Midtrans kadaluarsa', ['order_id' => $orderId]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Callback midtrans expire error: ' . $e->getMessage());
                }
                return response()->json(['status' => true, 'message' => 'Kadaluarsa']);

            default:
                Log::warning('Midtrans callback: unknown status', ['transaction_status' => $transactionStatus, 'order_id' => $orderId]);
                return response()->json(['status' => true, 'message' => 'Status tidak dikenal: ' . $transactionStatus]);
        }
    }

    // =============================================
    // 3C. HALAMAN SNAP — tampilkan ulang snap popup
    //     GET /bayar/snap/{snap_token}
    // =============================================
    public function snapPage(Request $request, string $snapToken)
    {
        $pembayaran = Pembayaran::with(['pendaftaran.siswa', 'pendaftaran.sekolah'])
            ->where('snap_token', $snapToken)
            ->first();

        if (!$pembayaran || $pembayaran->status_pembayaran !== 'pending') {
            return redirect()->route('pembayaran.index')
                ->with('info', 'Sesi pembayaran tidak ditemukan atau sudah selesai.');
        }

        $clientKey = config('midtrans.client_key');
        $snapJsUrl = config('midtrans.snap_js_url');
        $statusUrl = route('pembayaran.status', ['kode' => $pembayaran->pendaftaran->kode_regis]);

        return view('pembayaran.snap', compact(
            'pembayaran', 'snapToken', 'clientKey', 'snapJsUrl', 'statusUrl'
        ));
    }

    // =============================================
    // 3E. HALAMAN LANJUTKAN MIDTRANS — untuk pending
    //     GET /bayar/lanjut?kode=PPDB26-AB3XY7KZ
    // =============================================
    public function snapLanjut(Request $request)
    {
        $kode = $request->query('kode', '');

        if (!$kode) {
            return redirect()->route('pembayaran.index');
        }

        $pendaftaran = Pendaftaran::with(['siswa', 'sekolah'])
            ->where('kode_regis', strtoupper($kode))
            ->first();

        if (!$pendaftaran) {
            return redirect()->route('pembayaran.index')
                ->with('error', 'Nomor pendaftaran tidak ditemukan.');
        }

        // Cari pembayaran pending terbaru dengan snap_token
        $pembayaran = $pendaftaran->pembayarans()
            ->where('status_pembayaran', 'pending')
            ->whereNotNull('snap_token')
            ->latest()
            ->first();

        if (!$pembayaran || !$pembayaran->snap_token) {
            return redirect()
                ->route('pembayaran.cek') // redirect ke form bayar
                ->with('info', 'Tidak ada sesi pembayaran Midtrans yang aktif.');
        }

        $snapToken = $pembayaran->snap_token;
        $clientKey = config('midtrans.client_key');
        $snapJsUrl = config('midtrans.snap_js_url');
        $statusUrl = route('pembayaran.status', ['kode' => $kode]);

        return view('pembayaran.snap-lanjut', compact(
            'pembayaran', 'snapToken', 'clientKey', 'snapJsUrl', 'statusUrl'
        ));
    }

    // =============================================
    // 3F. RESET SNAP TOKEN — batalkan pending, kembali ke form
    //     POST /bayar/reset-snap
    // =============================================
    public function resetSnap(Request $request)
    {
        $kode = $request->query('kode', $request->input('kode', ''));

        if (!$kode) {
            return redirect()->route('pembayaran.index');
        }

        $pendaftaran = Pendaftaran::where('kode_regis', strtoupper($kode))->first();

        if (!$pendaftaran) {
            return redirect()->route('pembayaran.index')
                ->with('error', 'Nomor pendaftaran tidak ditemukan.');
        }

        // Mark semua pending sebagai gagal (di-cancel user)
        $updated = $pendaftaran->pembayarans()
            ->where('status_pembayaran', 'pending')
            ->update([
                'status_pembayaran' => 'gagal',
                'catatan'           => 'Dibatalkan oleh pengguna — memilih metode lain',
            ]);

        Log::info("Reset snap token for {$kode}: {$updated} record(s) cancelled by user");

        // Kembalikan status pendaftaran ke menunggu_pembayaran (agar bisa bayar lagi)
        if (in_array($pendaftaran->status, ['menunggu_pembayaran', 'diterima'])) {
            $pendaftaran->update(['status' => 'menunggu_pembayaran']);
        }

        // Redirect ke index dengan ?kode= — index blade sudah auto-submit via JS jika ada query kode
        return redirect()
            ->to(route('pembayaran.index') . '?kode=' . strtoupper($kode))
            ->with('success', 'Sesi Midtrans dibatalkan. Silakan pilih metode pembayaran lain.');
    }

    // =============================================
    // 3D. HALAMAN INFORMASI setelah redirect dari riplabs
    //     GET /bayar/informasi?order_id=...&status_code=...&transaction_status=...
    // =============================================
    public function informasi(Request $request)
    {
        $orderId           = $request->query('order_id', '');
        $transactionStatus = $request->query('transaction_status', '');

        if (!$orderId) {
            return redirect()->route('pembayaran.index');
        }

        $pembayaran = Pembayaran::with(['pendaftaran.siswa', 'pendaftaran.sekolah', 'pendaftaran.jurusan'])
            ->where('order_id', $orderId)
            ->first();

        if (!$pembayaran) {
            return redirect()->route('pembayaran.index')
                ->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $pendaftaran = $pembayaran->pendaftaran;
        $waAdmin     = self::WA_ADMIN[$pendaftaran->sekolah_id] ?? '6208993388681';

        return view('pembayaran.informasi', compact(
            'pembayaran', 'pendaftaran', 'transactionStatus', 'waAdmin'
        ));
    }

    // =============================================
    // 3G. HALAMAN SUKSES PEMBAYARAN MIDTRANS
    //     GET /bayar/sukses?kode=PPDB26-AB3XY7KZ
    // =============================================
    public function sukses(Request $request)
    {
        $kode = strtoupper(trim($request->query('kode', '')));

        if (!$kode) {
            return redirect()->route('pembayaran.index');
        }

        $pendaftaran = Pendaftaran::with([
            'siswa', 'sekolah', 'jurusan', 'waliSiswas',
            'pembayarans.metodePembayaran',
        ])->where('kode_regis', $kode)->first();

        if (!$pendaftaran) {
            return redirect()->route('pembayaran.index');
        }

        $pembayaran = $pendaftaran->pembayarans()
            ->where('status_pembayaran', 'sukses')
            ->latest()
            ->first();

        // Kalau belum sukses, tetap redirect ke status page biasa
        if (!$pembayaran) {
            return redirect()->route('pembayaran.status', ['kode' => $kode]);
        }

        $waAdmin = self::WA_ADMIN[$pendaftaran->sekolah_id] ?? '6208993388681';

        return view('pembayaran.sukses', compact('pendaftaran', 'pembayaran', 'waAdmin'));
    }

    // =============================================
    // 3H. AJAX — Update DB sukses dari JS onSuccess
    //     POST /bayar/payment-success  (fallback jika callback riplabs lambat)
    // =============================================
    public function handlePaymentSuccess(Request $request)
    {
        $orderId     = $request->input('order_id', '');
        $snapToken   = $request->input('snap_token', '');
        $paymentType = $request->input('payment_type', 'midtrans');

        if (!$orderId && !$snapToken) {
            return response()->json(['status' => false, 'message' => 'Parameter tidak lengkap'], 422);
        }

        $pembayaran = Pembayaran::with(['pendaftaran'])
            ->when($orderId,                   fn($q) => $q->where('order_id', $orderId))
            ->when(!$orderId && $snapToken,     fn($q) => $q->where('snap_token', $snapToken))
            ->first();

        if (!$pembayaran) {
            return response()->json(['status' => false, 'message' => 'Order tidak ditemukan'], 404);
        }

        // Sudah sukses sebelumnya (callback riplabs lebih cepat) — langsung return redirect
        if ($pembayaran->status_pembayaran === 'sukses') {
            return response()->json([
                'status'      => true,
                'already'     => true,
                'redirect_to' => route('pembayaran.sukses', ['kode' => $pembayaran->pendaftaran->kode_regis]),
            ]);
        }

        if ($pembayaran->status_pembayaran !== 'pending') {
            return response()->json(['status' => true, 'message' => 'Status: ' . $pembayaran->status_pembayaran]);
        }

        DB::beginTransaction();
        try {
            $pembayaran->update([
                'status_pembayaran'  => 'sukses',
                'verifikasi_tanggal' => now(),
            ]);
            $pembayaran->pendaftaran->update(['status' => 'pembayaran_lunas']);
            DB::commit();

            Log::info('Payment success via JS fallback', [
                'order_id'   => $orderId,
                'snap_token' => $snapToken,
            ]);

            try {
                set_time_limit(120);
                $this->kirimEmailPembayaranSukses($pembayaran->pendaftaran, $pembayaran, $paymentType);
            } catch (\Throwable $e) {
                Log::warning('Email sukses JS fallback gagal: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('handlePaymentSuccess error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'DB error'], 500);
        }

        return response()->json([
            'status'      => true,
            'redirect_to' => route('pembayaran.sukses', ['kode' => $pembayaran->pendaftaran->kode_regis]),
        ]);
    }

    // =============================================
    // 3I. DOWNLOAD PDF Formulir Pendaftaran
    //     GET /bayar/download-pdf?kode=PPDB26-AB3XY7KZ
    // =============================================
    public function downloadPdf(Request $request)
    {
        $kode = strtoupper(trim($request->query('kode', '')));

        if (!$kode) abort(404);

        $pendaftaran = Pendaftaran::with([
            'siswa', 'sekolah', 'jurusan', 'waliSiswas',
            'pembayarans.metodePembayaran',
        ])->where('kode_regis', $kode)->first();

        if (!$pendaftaran) abort(404, 'Pendaftaran tidak ditemukan.');

        $pembayaran = $pendaftaran->pembayarans()
            ->where('status_pembayaran', 'sukses')
            ->latest()
            ->first();

        if (!$pembayaran) abort(403, 'Formulir hanya tersedia setelah pembayaran lunas.');

        $namaMetode = $this->formatPaymentType(
            $pembayaran->metodePembayaran->nama_metode ?? 'midtrans'
        );

        $pdfOutput = $this->generatePdfLampiran($pendaftaran, $pembayaran, $namaMetode);

        if (!$pdfOutput) abort(500, 'Gagal membuat PDF. Silakan coba lagi.');

        return response($pdfOutput, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Formulir-Pendaftaran-' . $kode . '.pdf"',
        ]);
    }

    // =============================================
    // 4. SUBMIT PEMBAYARAN MANUAL (transfer/cash)
    // =============================================
    public function store(Request $request)
    {
        $request->validate([
            'kode_regis'           => 'required|string|exists:pendaftarans,kode_regis',
            'metode_pembayaran_id' => 'required|exists:metode_pembayarans,id',
            'proof'                => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'metode_pembayaran_id.required' => 'Pilih metode pembayaran terlebih dahulu.',
        ]);

        $pendaftaran = Pendaftaran::with(['siswa', 'sekolah', 'waliSiswas', 'pembayarans'])
            ->where('kode_regis', $request->kode_regis)
            ->firstOrFail();

        if (!in_array($pendaftaran->status, ['diterima', 'menunggu_pembayaran'])) {
            return back()->withErrors(['error' => 'Status pendaftaran tidak memenuhi syarat.']);
        }

        $sudahAda = $pendaftaran->pembayarans()
            ->whereIn('status_pembayaran', ['menunggu_verifikasi', 'sukses'])
            ->exists();
        if ($sudahAda) {
            return back()->withErrors(['error' => 'Sudah ada pembayaran yang sedang diproses atau sudah lunas.']);
        }

        $metode = MetodePembayaran::findOrFail($request->metode_pembayaran_id);

        if ($metode->nama_metode === 'transfer' && !$request->hasFile('proof')) {
            return back()->withErrors(['proof' => 'Bukti transfer wajib diupload.'])->withInput();
        }

        DB::beginTransaction();
        try {
            $proofPath = null;
            if ($request->hasFile('proof')) {
                $proofPath = $request->file('proof')
                    ->store('bukti_transfer/' . $pendaftaran->kode_regis, 'public');
            }

            $pembayaran = Pembayaran::create([
                'metode_pembayaran_id' => $metode->id,
                'pendaftaran_id'       => $pendaftaran->id,
                'nominal'              => self::NOMINAL,
                'status_pembayaran'    => 'menunggu_verifikasi',
                'tanggal_pembayaran'   => now()->toDateString(),
                'proof_path'           => $proofPath,
            ]);

            $pendaftaran->update(['status' => 'pembayaran_diproses']);
            DB::commit();

            try {
                set_time_limit(60);
                $this->kirimEmailPembayaran($pendaftaran, $pembayaran, $metode);
            } catch (\Throwable $e) {
                Log::warning('Email pembayaran manual gagal: ' . $e->getMessage());
            }

            return redirect()
                ->route('pembayaran.status', ['kode' => $pendaftaran->kode_regis])
                ->with('success', 'Pembayaran berhasil disubmit! Menunggu konfirmasi dari admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Pembayaran store error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan sistem. Silakan coba lagi.'])->withInput();
        }
    }

    // =============================================
    // 5. HALAMAN STATUS PEMBAYARAN
    // =============================================
    public function status(Request $request)
    {
        $kode = $request->query('kode');
        if (!$kode) return redirect()->route('pembayaran.index');

        $pendaftaran = Pendaftaran::with([
            'siswa', 'sekolah', 'jurusan',
            'pembayarans.metodePembayaran',
        ])->where('kode_regis', strtoupper($kode))->first();

        if (!$pendaftaran) return redirect()->route('pembayaran.index');

        $pembayaran = $pendaftaran->pembayarans()->latest()->first();
        $waAdmin    = self::WA_ADMIN[$pendaftaran->sekolah_id] ?? '6208993388681';

        return view('pembayaran.status', compact('pendaftaran', 'pembayaran', 'waAdmin'));
    }

    // =============================================
    // PRIVATE HELPERS
    // =============================================

    private function resolveMetodeId(string $paymentType): int
    {
        $midtransMetode = MetodePembayaran::where('nama_metode', 'otomatis')->first();
        return $midtransMetode?->id ?? 1;
    }

    private function kirimEmailPembayaran(
        Pendaftaran $pendaftaran,
        Pembayaran $pembayaran,
        MetodePembayaran $metode
    ): void {
        $adminEmails = $this->getAdminEmails();
        $kodeRegis   = $pendaftaran->kode_regis;
        $namaSiswa   = $pendaftaran->siswa->nama_siswa ?? '-';
        $namaSekolah = $pendaftaran->sekolah->nama_sekolah ?? '-';
        $nominal     = 'Rp ' . number_format(self::NOMINAL, 0, ',', '.');
        $namaMetode  = ucfirst($metode->nama_metode);
        $tanggal     = Carbon::parse($pembayaran->tanggal_pembayaran)->translatedFormat('d F Y');
        $waAdmin     = self::WA_ADMIN[$pendaftaran->sekolah_id] ?? '6208993388681';
        $waUrl       = 'https://wa.me/' . $waAdmin . '?text=' . urlencode(
            "Halo Admin PPDB, saya ingin konfirmasi pembayaran.\nKode: {$kodeRegis}\nNama: {$namaSiswa}"
        );
        $statusUrl    = route('pembayaran.status', ['kode' => $kodeRegis]);
        $adminPanelUrl = config('app.url') . '/admin/pembayarans';

        // ── Email ke siswa & wali: konfirmasi bukti diterima
        $emailSiswa = collect([$pendaftaran->siswa->email ?? null])
            ->merge($pendaftaran->waliSiswas->pluck('email'))
            ->filter()->unique()->values()->toArray();

        if (!empty($emailSiswa)) {
            $subjectSiswa = "Bukti Pembayaran Diterima - PPDB {$kodeRegis}";
            $htmlSiswa = $this->buildEmailPembayaran([
                'namaSiswa'   => $namaSiswa,
                'namaSekolah' => $namaSekolah,
                'kodeRegis'   => $kodeRegis,
                'nominal'     => $nominal,
                'namaMetode'  => $namaMetode,
                'tanggal'     => $tanggal,
                'statusLabel' => 'Menunggu Verifikasi Admin',
                'statusColor' => '#f59e0b',
                'waUrl'       => $waUrl,
                'statusUrl'   => $statusUrl,
            ]);
            foreach ($emailSiswa as $email) {
                Mail::html($htmlSiswa, fn($msg) => $msg->to($email)->subject($subjectSiswa)
                    ->from(config('mail.from.address'), config('mail.from.name')));
            }
        }

        // ── Email ke admin: notifikasi ada bukti transfer masuk, perlu diverifikasi
        if (!empty($adminEmails)) {
            $subjectAdmin = "[PERLU VERIFIKASI] Bukti Bayar Masuk — {$namaSiswa} ({$kodeRegis})";
            $htmlAdmin = "<!DOCTYPE html><html><head><meta charset='UTF-8'>
<style>body{font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:0}
.wrap{max-width:580px;margin:30px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.1)}
.hdr{background:#b45309;color:#fff;padding:22px 28px;text-align:center}
.hdr h1{margin:0;font-size:17px}.hdr p{margin:5px 0 0;opacity:.85;font-size:13px}
.body{padding:22px 28px}.info{background:#fef9c3;border-left:3px solid #b45309;border-radius:6px;padding:12px 14px;margin:12px 0;font-size:13px}
.row{display:table;width:100%;margin:5px 0;font-size:13px}
.lbl{display:table-cell;color:#666;width:140px}.val{display:table-cell;font-weight:600;color:#111}
.btn{display:inline-block;background:#0f4c3a;color:#fff;text-decoration:none;padding:11px 24px;border-radius:8px;font-weight:700;font-size:13px;margin-top:14px}
.ftr{background:#f9fafb;border-top:1px solid #eee;padding:12px 28px;text-align:center;color:#999;font-size:11px}
</style></head><body><div class='wrap'>
<div class='hdr'><h1>🔔 Bukti Pembayaran Baru Masuk</h1><p>Ada bukti transfer yang perlu diverifikasi</p></div>
<div class='body'>
<div class='info'>⚠️ Siswa berikut telah mengupload bukti pembayaran dan menunggu konfirmasi Anda.</div>
<div class='row'><span class='lbl'>Nama Siswa</span><span class='val'>{$namaSiswa}</span></div>
<div class='row'><span class='lbl'>Kode Pendaftaran</span><span class='val' style='font-family:monospace'>{$kodeRegis}</span></div>
<div class='row'><span class='lbl'>Sekolah Tujuan</span><span class='val'>{$namaSekolah}</span></div>
<div class='row'><span class='lbl'>Metode Bayar</span><span class='val'>{$namaMetode}</span></div>
<div class='row'><span class='lbl'>Nominal</span><span class='val'>{$nominal}</span></div>
<div class='row'><span class='lbl'>Tgl Upload</span><span class='val'>{$tanggal}</span></div>
<a href='{$adminPanelUrl}' class='btn' style='color:#fff !important'>🔍 Buka Panel Admin untuk Verifikasi →</a>
</div>
<div class='ftr'>Email otomatis dari sistem PPDB. Hanya admin yang menerima ini.</div>
</div></body></html>";
            foreach ($adminEmails as $email) {
                Mail::html($htmlAdmin, fn($msg) => $msg->to($email)->subject($subjectAdmin)
                    ->from(config('mail.from.address'), config('mail.from.name')));
            }
        }
    }

    private function kirimEmailPembayaranSukses(
        Pendaftaran $pendaftaran,
        Pembayaran $pembayaran,
        string $paymentType
    ): void {
        $kodeRegis   = $pendaftaran->kode_regis;
        $namaSiswa   = $pendaftaran->siswa->nama_siswa ?? '-';
        $namaSekolah = $pendaftaran->sekolah->nama_sekolah ?? '-';
        $namaJurusan = $pendaftaran->jurusan->nama_jurusan ?? 'Tidak Ada (SMP)';
        $nominal     = 'Rp ' . number_format(self::NOMINAL, 0, ',', '.');
        $namaMetode  = $this->formatPaymentType($paymentType);
        try {
            $tanggal = Carbon::now()->translatedFormat('d F Y');
        } catch (\Throwable $e) {
            $tanggal = Carbon::now()->format('d/m/Y');
        }
        $waAdmin   = self::WA_ADMIN[$pendaftaran->sekolah_id] ?? '6208993388681';
        $waUrl     = 'https://wa.me/' . $waAdmin . '?text=' . urlencode(
            "Halo Admin PPDB, pendaftaran saya sudah lunas.\nKode: {$kodeRegis}\nNama: {$namaSiswa}"
        );
        $statusUrl = route('pembayaran.status', ['kode' => $kodeRegis]);

        // Generate PDF - hanya untuk siswa
        $pdfAttachment = $this->generatePdfLampiran($pendaftaran, $pembayaran, $namaMetode);

        // ── 1. Email ke SISWA & WALI: konfirmasi lunas + PDF
        $emailSiswa = collect([$pendaftaran->siswa->email ?? null])
            ->merge($pendaftaran->waliSiswas->pluck('email'))
            ->filter()->unique()->values()->toArray();

        if (!empty($emailSiswa)) {
            $subjectSiswa = "Pembayaran Lunas - PPDB {$kodeRegis}";
            $htmlSiswa = $this->buildEmailPembayaranSukses([
                'namaSiswa'   => $namaSiswa,
                'namaSekolah' => $namaSekolah,
                'namaJurusan' => $namaJurusan,
                'kodeRegis'   => $kodeRegis,
                'nominal'     => $nominal,
                'namaMetode'  => $namaMetode,
                'tanggal'     => $tanggal,
                'waUrl'       => $waUrl,
                'statusUrl'   => $statusUrl,
                'jalur'       => ucfirst($pendaftaran->jalur_pendaftaran),
            ]);
            foreach ($emailSiswa as $email) {
                Mail::html($htmlSiswa, function ($msg) use ($email, $subjectSiswa, $pdfAttachment, $kodeRegis) {
                    $msg->to($email)->subject($subjectSiswa)
                        ->from(config('mail.from.address'), config('mail.from.name'));
                    if ($pdfAttachment) {
                        $msg->attachData($pdfAttachment, "Formulir-Pendaftaran-{$kodeRegis}.pdf", [
                            'mime' => 'application/pdf',
                        ]);
                    }
                });
            }
        }

        // ── 2. Email ke ADMIN: notif ringkas tanpa PDF
        $adminEmails = $this->getAdminEmails();
        if (!empty($adminEmails)) {
            $adminPanelUrl = config('app.url') . '/admin/pembayarans';
            $subjectAdmin  = "[LUNAS via Midtrans] {$namaSiswa} — {$kodeRegis}";
            $bodyAdmin     = "<!DOCTYPE html><html><head><meta charset='UTF-8'>
<style>body{font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:0}.wrap{max-width:560px;margin:30px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.1)}.hdr{background:#0f4c3a;color:#fff;padding:20px 24px;text-align:center}.body{padding:20px 24px}.row{display:table;width:100%;margin:6px 0;font-size:13px}.lbl{display:table-cell;color:#666;width:150px}.val{display:table-cell;font-weight:600;color:#111}.btn{display:inline-block;background:#0f4c3a;color:#fff;text-decoration:none;padding:10px 22px;border-radius:8px;font-weight:700;font-size:13px;margin-top:14px}.ftr{background:#f9fafb;border-top:1px solid #eee;padding:12px 24px;text-align:center;color:#999;font-size:11px}</style>
</head><body><div class='wrap'>
<div class='hdr'><h1 style='margin:0;font-size:17px'>Pembayaran Lunas via Midtrans</h1><p style='margin:4px 0 0;opacity:.85;font-size:12px'>Otomatis dikonfirmasi oleh sistem</p></div>
<div class='body'>
<div class='row'><span class='lbl'>Nama Siswa</span><span class='val'>{$namaSiswa}</span></div>
<div class='row'><span class='lbl'>Kode Pendaftaran</span><span class='val' style='font-family:monospace'>{$kodeRegis}</span></div>
<div class='row'><span class='lbl'>Sekolah Tujuan</span><span class='val'>{$namaSekolah}</span></div>
<div class='row'><span class='lbl'>Metode Bayar</span><span class='val'>{$namaMetode}</span></div>
<div class='row'><span class='lbl'>Nominal</span><span class='val'>{$nominal}</span></div>
<div class='row'><span class='lbl'>Tanggal</span><span class='val'>{$tanggal}</span></div>
<a href='{$adminPanelUrl}' class='btn' style='color:#fff !important'>Lihat di Panel Admin</a>
</div>
<div class='ftr'>Notifikasi otomatis sistem PPDB. Hanya admin yang menerima ini.</div>
</div></body></html>";
            foreach ($adminEmails as $email) {
                Mail::html($bodyAdmin, fn($msg) => $msg->to($email)->subject($subjectAdmin)
                    ->from(config('mail.from.address'), config('mail.from.name')));
            }
        }
    }

    private function formatPaymentType(string $type): string
    {
        $map = [
            'bank_transfer'  => 'Transfer Bank',
            'credit_card'    => 'Kartu Kredit',
            'cstore'         => 'Minimarket (Indomaret/Alfamart)',
            'echannel'       => 'Mandiri Bill',
            'bca_klikpay'    => 'BCA KlikPay',
            'gopay'          => 'GoPay',
            'shopeepay'      => 'ShopeePay',
            'qris'           => 'QRIS',
            'akulaku'        => 'Akulaku',
        ];
        return $map[$type] ?? ucwords(str_replace('_', ' ', $type));
    }

    // Public wrapper - dipanggil dari PembayaransTable
    public function generatePdfLampiranPublic(
        Pendaftaran $pendaftaran,
        Pembayaran $pembayaran,
        string $namaMetode
    ): ?string {
        return $this->generatePdfLampiran($pendaftaran, $pembayaran, $namaMetode);
    }

    private function generatePdfLampiran(
        Pendaftaran $pendaftaran,
        Pembayaran $pembayaran,
        string $namaMetode
    ): ?string {
        try {
            $kodeRegis    = $pendaftaran->kode_regis;
            $namaSiswa    = $pendaftaran->siswa->nama_siswa ?? '-';
            $nisn         = $pendaftaran->siswa->nisn ?? '-';
            $namaSekolah  = $pendaftaran->sekolah->nama_sekolah ?? '-';
            $namaJurusan  = $pendaftaran->jurusan->nama_jurusan ?? 'Tidak Ada (SMP)';
            $jalur        = ucfirst($pendaftaran->jalur_pendaftaran);
            $nominal      = 'Rp ' . number_format(self::NOMINAL, 0, ',', '.');
            $tanggalBayar = Carbon::parse($pembayaran->verifikasi_tanggal ?? $pembayaran->updated_at ?? now())->translatedFormat('d F Y, H:i');
            $tanggalDaftar = Carbon::parse($pendaftaran->tanggal_submit)->translatedFormat('d F Y');
            $orderId      = $pembayaran->order_id ?? '-';

            $waliList = $pendaftaran->waliSiswas->map(function ($w) {
                return ucfirst($w->hubungan) . ': ' . $w->nama_wali . ' (' . $w->pekerjaan . ')';
            })->implode('<br>');

            // Data tambahan siswa
            $siswa         = $pendaftaran->siswa;
            $jk            = ($siswa->jk ?? '') === 'laki_laki' ? 'Laki-Laki' : 'Perempuan';
            $agama         = ucfirst($siswa->agama ?? '-');
            $tempatLahir   = $siswa->tempat_lahir ?? '-';
            $tanggalLahir  = $siswa->tanggal_lahir
                ? Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y')
                : '-';
            $alamatSiswa   = $siswa->alamat ?? '-';
            $phoneSiswa    = $siswa->phone ?? '-';
            $emailSiswa    = $siswa->email ?? '-';
            $asalSekolah   = $siswa->asal_sekolah ?? '-';
            $tahunLulus    = $siswa->tahun_lulus ?? '-';
            $nomorIjazah   = $siswa->nomor_ijazah ?? '-';
            $jalurPend     = $pendaftaran->jalur_pendaftaran;
            $ketJalur      = $pendaftaran->ket_jalur_pendaftaran ?? null;

            // Data sekolah
            $sekolah         = $pendaftaran->sekolah;
            $sekolahAlamat   = $sekolah->alamat ?? '-';
            $sekolahTingkat  = strtoupper($sekolah->tingkatan ?? '');
            $jurusan         = $pendaftaran->jurusan;

            // Prosedur PPDB berikutnya berdasarkan status
            $catatan = $pembayaran->catatan ?? null;

            $html = '<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  @page { margin: 25mm 22mm 25mm 22mm; size: A4 portrait; }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9.5pt; color: #1a1a1a; background: #fff; padding: 8mm 8mm 8mm 8mm; }

  .hdr { background: #0f4c3a; color: #fff; padding: 14px 18px 12px; border-radius: 6px 6px 0 0; }
  .hdr-inner { display: table; width: 100%; }
  .hdr-left  { display: table-cell; vertical-align: middle; }
  .hdr-right { display: table-cell; vertical-align: middle; text-align: right; width: 90pt; }
  .hdr h1 { font-size: 14pt; font-weight: bold; }
  .hdr-sub { font-size: 8pt; opacity: .85; margin-top: 3px; }
  .hdr-badge { background: #22c55e; color: #fff; border-radius: 4px; padding: 5px 12px; font-size: 9pt; font-weight: bold; }

  .banner { background: #dcfce7; border: 1.5px solid #16a34a; border-radius: 6px; padding: 9px 14px; text-align: center; color: #166534; font-weight: bold; font-size: 10pt; }

  .kode-box { border: 2.5px solid #0f4c3a; border-radius: 8px; padding: 14px 12px; text-align: center; background: #f0fdf4; }
  .kode-label { font-size: 7.5pt; color: #555; text-transform: uppercase; letter-spacing: 1.5px; }
  .kode { font-size: 24pt; font-weight: bold; letter-spacing: 6px; font-family: Courier, monospace; color: #0f4c3a; }
  .kode-sub { font-size: 8pt; color: #777; }

  .sec { font-size: 9pt; font-weight: bold; color: #fff; background: #0f4c3a; padding: 6px 10px; }

  table.info { width: 100%; border-collapse: collapse; font-size: 9pt; }
  table.info tr { border-bottom: 1px solid #f0f0f0; }
  table.info td { padding: 5px 6px; vertical-align: top; line-height: 1.5; }
  table.info td.lbl { color: #666; width: 42%; }
  table.info td.val { font-weight: bold; color: #111; }

  .catatan { background: #fef9c3; border-left: 3px solid #ca8a04; padding: 10px 12px; font-size: 9pt; }

  .steps { width: 100%; border-collapse: collapse; }
  .steps td { padding: 7px 8px; vertical-align: top; font-size: 9pt; border-bottom: 1px solid #f0fdf4; line-height: 1.5; }
  .snum { background: #0f4c3a; color: #fff; font-weight: bold; text-align: center; width: 20pt; border-radius: 3px; }

  .footer { border-top: 1px solid #d1d5db; padding-top: 8px; font-size: 7.5pt; color: #9ca3af; text-align: center; }
</style>
</head>
<body>
<div style="background:#fff; padding:0;">

<div class="hdr">
  <div class="hdr-inner">
    <div class="hdr-left">
      <h1>Yayasan Fatahillah</h1>
      <div class="hdr-sub">PPDB Online &mdash; Tahun Ajaran 2026/2027 &mdash; Formulir Pendaftaran Siswa Baru</div>
    </div>
    <div class="hdr-right"><span class="hdr-badge">LUNAS</span></div>
  </div>
</div>

<div style="height:8px"></div>

<div class="banner">PEMBAYARAN DIKONFIRMASI LUNAS &mdash; PENDAFTARAN AKTIF</div>

<br>

<div class="kode-box">
  <div class="kode-label">Nomor Kode Pendaftaran</div>
  <br>
  <div class="kode">' . $kodeRegis . '</div>
  <br>
  <div class="kode-sub">Tunjukkan kode ini kepada panitia PPDB saat datang ke sekolah</div>
</div>

<br><br>

<table style="width:100%;border-collapse:collapse;">
<tr>

<td style="width:50%;padding-right:12pt;vertical-align:top;">

  <div class="sec">Data Diri Siswa</div>
  <table class="info">
    <tr><td class="lbl">Nama Lengkap</td><td class="val">' . $namaSiswa . '</td></tr>
    <tr><td class="lbl">NISN</td><td class="val">' . $nisn . '</td></tr>
    <tr><td class="lbl">Jenis Kelamin</td><td class="val">' . $jk . '</td></tr>
    <tr><td class="lbl">Tempat, Tgl Lahir</td><td class="val">' . $tempatLahir . ', ' . $tanggalLahir . '</td></tr>
    <tr><td class="lbl">Agama</td><td class="val">' . $agama . '</td></tr>
    <tr><td class="lbl">Alamat</td><td class="val">' . $alamatSiswa . '</td></tr>
    <tr><td class="lbl">No. Telepon</td><td class="val">' . $phoneSiswa . '</td></tr>
    <tr><td class="lbl">Email</td><td class="val" style="font-size:8pt">' . $emailSiswa . '</td></tr>
    <tr><td class="lbl">Asal Sekolah</td><td class="val">' . $asalSekolah . '</td></tr>
    <tr><td class="lbl">Tahun Lulus</td><td class="val">' . $tahunLulus . '</td></tr>
    <tr><td class="lbl">Nomor Ijazah</td><td class="val" style="font-size:8pt">' . $nomorIjazah . '</td></tr>
  </table>

  <br>

  <div class="sec">Data Orang Tua / Wali</div>
  <table class="info">' .
    $pendaftaran->waliSiswas->map(function ($w) {
        $hub  = ucfirst(str_replace('_', ' ', $w->hubungan ?? '-'));
        $rows = "<tr><td class='lbl'>{$hub}</td><td class='val'>{$w->nama_wali}</td></tr>"
              . "<tr><td class='lbl' style='padding-left:12px;font-size:8.5pt;color:#888'>Pekerjaan</td><td class='val' style='color:#444'>{$w->pekerjaan}</td></tr>";
        if ($w->notelp_wali) {
            $rows .= "<tr><td class='lbl' style='padding-left:12px;font-size:8.5pt;color:#888'>No. Telepon</td><td class='val' style='color:#444'>{$w->notelp_wali}</td></tr>";
        }
        return $rows;
    })->implode('') . '
  </table>

</td>

<td style="width:50%;padding-left:12pt;vertical-align:top;">

  <div class="sec">Informasi Sekolah Tujuan</div>
  <table class="info">
    <tr><td class="lbl">Nama Sekolah</td><td class="val">' . $namaSekolah . '</td></tr>
    <tr><td class="lbl">Tingkatan</td><td class="val">' . $sekolahTingkat . '</td></tr>
    <tr><td class="lbl">Jurusan</td><td class="val">' . $namaJurusan . '</td></tr>
    <tr><td class="lbl">Alamat Sekolah</td><td class="val" style="font-size:8.5pt">' . $sekolahAlamat . '</td></tr>
  </table>

  <br>

  <div class="sec">Info Pendaftaran</div>
  <table class="info">
    <tr><td class="lbl">Kode Pendaftaran</td><td class="val" style="font-family:Courier,monospace">' . $kodeRegis . '</td></tr>
    <tr><td class="lbl">Jalur Pendaftaran</td><td class="val">' . ucfirst($jalurPend) . '</td></tr>
    <tr><td class="lbl">Tanggal Mendaftar</td><td class="val">' . $tanggalDaftar . '</td></tr>
    ' . ($ketJalur ? '<tr><td class="lbl">Ket. Jalur</td><td class="val" style="font-size:8.5pt">' . $ketJalur . '</td></tr>' : '') . '
  </table>

  <br>

  <div class="sec">Rincian Pembayaran</div>
  <table class="info">
    <tr><td class="lbl">Nominal</td><td class="val">' . $nominal . '</td></tr>
    <tr><td class="lbl">Metode</td><td class="val">' . $namaMetode . '</td></tr>
    <tr><td class="lbl">Tanggal Lunas</td><td class="val">' . $tanggalBayar . '</td></tr>
    <tr><td class="lbl">Order ID</td><td class="val" style="font-size:8pt;color:#555">' . $orderId . '</td></tr>
    <tr><td class="lbl">Status</td><td class="val" style="color:#166534;font-weight:bold">LUNAS</td></tr>
  </table>

</td>
</tr>
</table>

' . ($catatan ? '<br><div class="catatan"><strong>Catatan Admin:</strong> ' . $catatan . '</div>' : '') . '

<div style="page-break-before:always;"></div>

<div class="sec">Prosedur PPDB Selanjutnya</div>
<br>
<table class="steps">
  <tr><td class="snum">1</td><td><strong>Cetak dokumen ini</strong> &mdash; Bawa formulir ini sebagai bukti resmi pendaftaran dan pembayaran lunas.</td></tr>
  <tr><td class="snum">2</td><td><strong>Siapkan dokumen asli</strong> &mdash; Ijazah/SKL, Kartu Keluarga, Akta Kelahiran, Pas Foto 3x4 (berwarna, 4 lembar), dan SKHUN jika ada.</td></tr>
  <tr><td class="snum">3</td><td><strong>Datang ke ' . $namaSekolah . '</strong> &mdash; Serahkan formulir ini beserta dokumen asli kepada petugas Tata Usaha sesuai jadwal daftar ulang.</td></tr>
  <tr><td class="snum">4</td><td><strong>Tunjukkan kode ' . $kodeRegis . '</strong> kepada petugas untuk verifikasi data pendaftaran di sistem.</td></tr>
  <tr><td class="snum">5</td><td><strong>Ikuti jadwal sekolah</strong> &mdash; Pantau pengumuman jadwal orientasi siswa baru, pembagian seragam, dan kegiatan awal tahun ajaran 2026/2027.</td></tr>
</table>

<br><br>

<div class="footer">
  PPDB Online Yayasan Fatahillah &bull;
  Dicetak: ' . Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB &bull;
  Pertanyaan: info@fatahillah.sch.id
</div>

</div>
</body>
</html>';

                        // Dompdf direct - fix "Cannot resolve public path" di Hostinger
                        $options = new \Dompdf\Options();
                        $options->setIsHtml5ParserEnabled(true);
                        $options->setIsRemoteEnabled(false);
                        $options->setFontDir(storage_path('fonts/'));
                        $options->setFontCache(storage_path('fonts/'));
                        $options->setTempDir(sys_get_temp_dir());
                        $options->setChroot(base_path());
                        $options->setDefaultFont('DejaVu Sans');
                        $options->setIsFontSubsettingEnabled(true);
                        $options->setDpi(96);
                        $dompdf = new \Dompdf\Dompdf($options);
                        $dompdf->loadHtml($html, 'UTF-8');
                        $dompdf->setPaper('A4', 'portrait');
                        $dompdf->render();
            return $dompdf->output();

        } catch (\Throwable $e) {
            Log::error('Generate PDF lampiran gagal: ' . $e->getMessage());
            return null;
        }
    }

    private function buildEmailPembayaran(array $d): string
    {
        return "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'>
<style>
body{font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:0}
.wrap{max-width:600px;margin:30px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.1)}
.hdr{background:linear-gradient(135deg,#0f4c3a,#1a6b55);color:#fff;padding:36px 30px;text-align:center}
.hdr h1{margin:0;font-size:22px}.hdr p{margin:6px 0 0;opacity:.85;font-size:14px}
.badge{display:inline-block;background:rgba(255,255,255,.2);border-radius:20px;padding:4px 14px;font-size:13px;margin-top:10px}
.body{padding:28px 30px}
.kode-box{background:#0f4c3a;color:#fff;border-radius:10px;padding:18px;text-align:center;margin:20px 0}
.kode{font-size:28px;font-weight:700;letter-spacing:4px;font-family:monospace}
.info{background:#f0faf8;border-left:4px solid #3d9080;border-radius:8px;padding:18px;margin:16px 0}
.row{display:flex;margin-bottom:8px;font-size:13px}
.lbl{color:#666;width:150px;flex-shrink:0}.val{font-weight:600;color:#111}
.btn{display:inline-block;text-decoration:none;padding:12px 24px;border-radius:8px;font-weight:700;font-size:13px;margin:6px 4px}
.btn-wa{background:#25D366;color:#fff}.btn-cek{background:#3d9080;color:#fff}
.ftr{background:#f9fafb;border-top:1px solid #eee;padding:18px 30px;text-align:center;color:#999;font-size:12px}
</style></head><body><div class='wrap'>
<div class='hdr'><h1>🎓 Yayasan Fatahillah</h1><p>PPDB Online — Tahun Ajaran 2026/2027</p>
<span class='badge'>💳 Pembayaran Diterima</span></div>
<div class='body'>
<p>Assalamualaikum, <strong>{$d['namaSiswa']}</strong>.</p>
<p>Pembayaran pendaftaran PPDB Anda telah kami terima dan sedang menunggu konfirmasi admin.</p>
<div class='kode-box'><div class='kode'>{$d['kodeRegis']}</div>
<small style='opacity:.7;font-size:12px;display:block;margin-top:4px'>Nomor Pendaftaran</small></div>
<div class='info'>
<div class='row'><span class='lbl'>Nama Siswa</span><span class='val'>{$d['namaSiswa']}</span></div>
<div class='row'><span class='lbl'>Sekolah Tujuan</span><span class='val'>{$d['namaSekolah']}</span></div>
<div class='row'><span class='lbl'>Nominal</span><span class='val'>{$d['nominal']}</span></div>
<div class='row'><span class='lbl'>Metode Pembayaran</span><span class='val'>{$d['namaMetode']}</span></div>
<div class='row'><span class='lbl'>Tanggal Bayar</span><span class='val'>{$d['tanggal']}</span></div>
<div class='row'><span class='lbl'>Status</span><span class='val'>⏳ {$d['statusLabel']}</span></div>
</div>
<div style='text-align:center;margin:20px 0'>
<a href='{$d['waUrl']}' style='color:white !important;' class='btn btn-wa'>💬 Hubungi Admin WA</a>
<a href='{$d['statusUrl']}' style='color:white !important;' class='btn btn-cek'>🔍 Cek Status</a>
</div>
</div>
<div class='ftr'><p>&copy; " . date('Y') . " Yayasan Fatahillah.</p><p>Email ini dikirim otomatis.</p></div>
</div></body></html>";
    }

    private function buildEmailPembayaranSukses(array $d): string
    {
        return "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'>
<style>
body{font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:0}
.wrap{max-width:600px;margin:30px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.1)}
.hdr{background:linear-gradient(135deg,#0f4c3a,#1a6b55);color:#fff;padding:36px 30px;text-align:center}
.hdr h1{margin:0;font-size:22px}.hdr p{margin:6px 0 0;opacity:.85;font-size:14px}
.badge{display:inline-block;background:#25D366;border-radius:20px;padding:4px 14px;font-size:13px;margin-top:10px;color:#fff;font-weight:700}
.body{padding:28px 30px}
.kode-box{background:#0f4c3a;color:#fff;border-radius:10px;padding:20px;text-align:center;margin:20px 0}
.kode{font-size:32px;font-weight:700;letter-spacing:6px;font-family:monospace}
.info{background:#f0faf8;border-left:4px solid #3d9080;border-radius:8px;padding:18px;margin:16px 0}
.row{display:flex;margin-bottom:8px;font-size:13px}
.lbl{color:#666;width:150px;flex-shrink:0}.val{font-weight:600;color:#111}
.steps{margin:16px 0}
.step{display:flex;gap:12px;margin-bottom:14px;align-items:flex-start}
.stepnum{background:#1a6b55;color:#fff;border-radius:50%;width:26px;min-width:26px;height:26px;font-size:11px;font-weight:700;text-align:center;line-height:26px;flex-shrink:0}
.steptxt{font-size:13px;line-height:1.6}
.btn{display:inline-block;text-decoration:none;padding:12px 24px;border-radius:8px;font-weight:700;font-size:13px;margin:6px 4px}
.btn-wa{background:#25D366;color:#fff}.btn-cek{background:#3d9080;color:#fff}
.lunas{background:#dcfce7;border:2px solid #4ade80;border-radius:10px;padding:14px;text-align:center;color:#166534;font-weight:700;font-size:15px;margin-bottom:16px}
.ftr{background:#f9fafb;border-top:1px solid #eee;padding:18px 30px;text-align:center;color:#999;font-size:12px}
</style></head><body><div class='wrap'>
<div class='hdr'><h1>🎓 Yayasan Fatahillah</h1><p>PPDB Online — Tahun Ajaran 2026/2027</p>
<span class='badge'>✅ Pembayaran Berhasil & Pendaftaran Selesai</span></div>
<div class='body'>
<p>Assalamualaikum, <strong>{$d['namaSiswa']}</strong>.</p>
<div class='lunas'>🎉 SELAMAT! Pembayaran PPDB Anda telah LUNAS</div>
<p style='font-size:13px;color:#555'>Pembayaran pendaftaran Anda telah berhasil diproses. Simpan nomor pendaftaran berikut dan bawa dokumen ini saat datang ke sekolah.</p>
<div class='kode-box'>
  <div class='kode'>{$d['kodeRegis']}</div>
  <small style='opacity:.75;font-size:11px;display:block;margin-top:6px'>Nomor Kode Pendaftaran — Tunjukkan ke panitia PPDB</small>
</div>
<div class='info'>
<div class='row'><span class='lbl'>Nama Siswa</span><span class='val'>{$d['namaSiswa']}</span></div>
<div class='row'><span class='lbl'>Sekolah Tujuan</span><span class='val'>{$d['namaSekolah']}</span></div>
<div class='row'><span class='lbl'>Jurusan</span><span class='val'>{$d['namaJurusan']}</span></div>
<div class='row'><span class='lbl'>Jalur</span><span class='val'>{$d['jalur']}</span></div>
<div class='row'><span class='lbl'>Nominal Dibayar</span><span class='val'>{$d['nominal']}</span></div>
<div class='row'><span class='lbl'>Metode Pembayaran</span><span class='val'>{$d['namaMetode']}</span></div>
<div class='row'><span class='lbl'>Tanggal Lunas</span><span class='val'>{$d['tanggal']}</span></div>
<div class='row'><span class='lbl'>Status</span><span class='val' style='color:#166534'>✅ LUNAS</span></div>
</div>
<h3 style='font-size:14px;color:#0f4c3a;margin-bottom:10px'>📌 Langkah Selanjutnya:</h3>
<div class='steps'>
<div class='step'><div class='stepnum'>1</div><div class='steptxt'><strong>Simpan & Cetak Email ini</strong><br><small style='color:#666'>Dokumen PDF terlampir di email ini sebagai bukti resmi pembayaran lunas.</small></div></div>
<div class='step'><div class='stepnum'>2</div><div class='steptxt'><strong>Datang ke {$d['namaSekolah']}</strong><br><small style='color:#666'>Bawa nomor pendaftaran <strong>{$d['kodeRegis']}</strong> dan dokumen asli ke bagian Tata Usaha sekolah.</small></div></div>
<div class='step'><div class='stepnum'>3</div><div class='steptxt'><strong>Serahkan Dokumen ke Panitia</strong><br><small style='color:#666'>Ijazah asli, KK, Akta Kelahiran, dan bukti pembayaran ini diserahkan ke petugas PPDB.</small></div></div>
<div class='step'><div class='stepnum'>4</div><div class='steptxt'><strong>Ikuti Jadwal Sekolah</strong><br><small style='color:#666'>Pantau jadwal daftar ulang, pembagian seragam, dan MOS/MPLS dari sekolah.</small></div></div>
</div>
<p style='font-size:12px;color:#888;margin-top:8px'>📎 Dokumen PDF bukti pendaftaran & pembayaran terlampir di email ini.</p>
<div style='text-align:center;margin:20px 0'>
<a href='{$d['waUrl']}' style='color:white !important;' class='btn btn-wa'>💬 Konfirmasi via WA Admin</a>
<a href='{$d['statusUrl']}' style='color:white !important;' class='btn btn-cek'>🔍 Lihat Status Online</a>
</div>
</div>
<div class='ftr'><p>&copy; " . date('Y') . " Yayasan Fatahillah. Jl. Fatahillah No.1, Cilegon, Banten.</p>
<p>Email ini dikirim otomatis. Pertanyaan: info@fatahillah.sch.id</p></div>
</div></body></html>";
    }
}