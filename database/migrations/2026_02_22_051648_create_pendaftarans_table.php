<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_regis', 16)->unique();
            $table->foreignId('tahun_akademik_id')->nullable()->constrained('tahun_akademiks')->nullOnDelete();
            $table->foreignId('sekolah_id')->nullable()->constrained('sekolahs')->nullOnDelete();
            $table->foreignId('jurusan_id')->nullable()->constrained('jurusans')->nullOnDelete();

            $table->enum('jalur_pendaftaran', [
                'reguler',
                'prestasi',
                'afirmasi',
                'pindahan',
            ]);

            $table->enum('status', [
                'diproses',
                'diverifikasi',
                'diterima',
                'ditolak',
                'menunggu_pembayaran',
                'pembayaran_diproses',
                'pembayaran_lunas',
                'selesai',
            ])->default('diproses');

            $table->date('tanggal_submit');
            $table->enum('dibuat_oleh', ['publik', 'admin'])->default('publik');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftarans');
    }
};
