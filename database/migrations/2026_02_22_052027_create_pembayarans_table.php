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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('metode_pembayaran_id')->nullable()->constrained('metode_pembayarans')->nullOnDelete();
            $table->foreignId('pendaftaran_id')->nullable()->constrained('pendaftarans')->nullOnDelete();
            $table->unsignedBigInteger('nominal');
            $table->string('order_id')->nullable()->unique();
            $table->string('snap_token')->nullable();
            $table->enum('status_pembayaran', [
                'pending',
                'menunggu_verifikasi',
                'sukses',
                'gagal',
                'kadaluarsa',
            ])->default('pending');
            $table->date('tanggal_pembayaran')->nullable();
            $table->string('proof_path')->nullable();
            $table->foreignId('verifikasi_oleh')->nullable()->constrained('users');
            $table->timestamp('verifikasi_tanggal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
