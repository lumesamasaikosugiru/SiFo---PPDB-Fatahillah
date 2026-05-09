
<?php
// Tambahkan SEMENTARA di routes/web.php untuk test SMTP
// Akses: http://localhost:8000/test-email
// HAPUS setelah selesai testing!

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

Route::get('/test-email', function () {
    $hasil = [];

    // 1. Cek config mail yang aktif
    $hasil['mailer']     = config('mail.default');
    $hasil['host']       = config('mail.mailers.smtp.host');
    $hasil['port']       = config('mail.mailers.smtp.port');
    $hasil['scheme']     = config('mail.mailers.smtp.scheme');
    $hasil['username']   = config('mail.mailers.smtp.username');
    $hasil['from']       = config('mail.from.address');
    $hasil['queue']      = config('queue.default');
    $hasil['admin_email']= env('ADMIN_EMAIL');

    // 2. Coba kirim email test
    try {
        Mail::html(
            '<h2>Test Email PPDB</h2><p>Jika kamu baca ini, SMTP berhasil!</p><p>Waktu: ' . now() . '</p>',
            function ($msg) {
                $msg->to(env('ADMIN_EMAIL', 'test@example.com'))
                    ->subject('✅ Test Email PPDB - ' . now()->format('H:i:s'))
                    ->from(config('mail.from.address'), config('mail.from.name'));
            }
        );
        $hasil['status'] = '✅ BERHASIL KIRIM';
    } catch (\Throwable $e) {
        $hasil['status'] = '❌ GAGAL: ' . $e->getMessage();
        Log::error('Test email gagal: ' . $e->getMessage());
    }

    // 3. Tampilkan hasil
    echo '<pre style="font-family:monospace;font-size:14px;padding:20px">';
    echo "=== SMTP TEST RESULT ===\n\n";
    foreach ($hasil as $k => $v) {
        echo str_pad($k, 20) . ": " . $v . "\n";
    }
    echo '</pre>';
});
