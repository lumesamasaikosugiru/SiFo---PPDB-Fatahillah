<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * MySQL tidak bisa langsung drop unique index yang menjadi sandaran FK constraint.
     * Urutan yang benar:
     *   1. Drop FK constraint (wali_siswas_pendaftaran_id_foreign)
     *   2. Drop unique index (wali_siswas_pendaftaran_id_unique)
     *   3. Recreate FK biasa tanpa unique — sehingga 1 pendaftaran bisa punya banyak wali
     *   4. Jadikan kolom 'alamat' nullable — form publik tidak mengumpulkan alamat wali
     */
    public function up(): void
    {
        Schema::table('wali_siswas', function (Blueprint $table) {
            // Step 1 — lepas FK dulu agar unique index bisa di-drop
            $table->dropForeign(['pendaftaran_id']);

            // Step 2 — drop unique index
            $table->dropUnique(['pendaftaran_id']);

            // Step 3 — pasang kembali FK biasa (non-unique) dengan cascadeOnDelete
            $table->foreign('pendaftaran_id')
                  ->references('id')
                  ->on('pendaftarans')
                  ->cascadeOnDelete();

            // Step 4 — jadikan alamat nullable
            $table->text('alamat')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('wali_siswas', function (Blueprint $table) {
            $table->dropForeign(['pendaftaran_id']);
            $table->unique('pendaftaran_id');
            $table->foreign('pendaftaran_id')
                  ->references('id')
                  ->on('pendaftarans')
                  ->cascadeOnDelete();
            $table->text('alamat')->nullable(false)->change();
        });
    }
};

