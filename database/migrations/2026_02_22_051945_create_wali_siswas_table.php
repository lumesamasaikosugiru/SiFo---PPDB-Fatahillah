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
        Schema::create('wali_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('nama_wali', 50);
            $table->enum('hubungan', [
                'orang_tua',
                'saudara_kandung',
                'saudara_keluarga',
            ]);
            $table->string('pekerjaan', 30);
            $table->string('notelp_wali', 15);
            $table->string('email', 30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wali_siswas');
    }
};
