<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE pendaftarans 
            MODIFY status ENUM(
                'diproses',
                'diverifikasi',
                'diterima',
                'ditolak',
                'menunggu_pembayaran',
                'pembayaran_diproses',
                'pembayaran_lunas',
                'selesai'
            ) DEFAULT 'diproses'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE pendaftarans 
            MODIFY status ENUM(
                'diproses',
                'diverifikasi',
                'diterima',
                'ditolak',
                'menunggu_pembayaran',
                'pembayaran_lunas'
            ) DEFAULT 'diproses'
        ");
    }
};