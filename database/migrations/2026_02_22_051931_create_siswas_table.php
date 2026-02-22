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
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->unique()->constrained('pendaftarans')->cascadeOnDelete();
            $table->string('nisn', 10);
            $table->string('nama_siswa', 50);
            $table->enum('jk', ['laki_laki', 'perempuan']);
            $table->string('phone', 15);
            $table->string('email', 50);
            $table->enum('agama', [
                'islam',
                'protestan',
                'katolik',
                'hindu',
                'budha',
                'khonghucu',
            ]);
            $table->string('tempat_lahir', 50);
            $table->date('tanggal_lahir');
            $table->string('asal_sekolah', 50);
            $table->unsignedSmallInteger('tahun_lulus');
            $table->string('nomor_ijazah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
