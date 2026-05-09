<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hapus unique constraint pada nisn, phone, email di tabel siswas.
 * Alasan: siswa yang sama bisa mendaftar ke lebih dari satu sekolah,
 * sehingga NISN/phone/email tidak harus unik secara global.
 * Yang unik adalah kombinasi pendaftaran_id (sudah unique per pendaftaran).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropUnique('siswas_nisn_unique');
            $table->dropUnique('siswas_phone_unique');
            $table->dropUnique('siswas_email_unique');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->unique('nisn');
            $table->unique('phone');
            $table->unique('email');
        });
    }
};
