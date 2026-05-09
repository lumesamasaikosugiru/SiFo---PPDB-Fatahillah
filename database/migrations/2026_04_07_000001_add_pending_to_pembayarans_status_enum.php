<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah 'pending' ke enum status_pembayaran
        // Dibutuhkan untuk Midtrans snap token yang belum selesai dibayar
        DB::statement("ALTER TABLE pembayarans MODIFY COLUMN status_pembayaran ENUM('pending','menunggu_verifikasi','sukses','gagal','kadaluarsa') NOT NULL DEFAULT 'menunggu_verifikasi'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pembayarans MODIFY COLUMN status_pembayaran ENUM('menunggu_verifikasi','sukses','gagal','kadaluarsa') NOT NULL DEFAULT 'menunggu_verifikasi'");
    }
};
