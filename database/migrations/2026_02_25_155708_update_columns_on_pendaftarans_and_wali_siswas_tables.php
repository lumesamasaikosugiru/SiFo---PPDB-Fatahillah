<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->string('kode_regis', 20)->change();
        });

        Schema::table('wali_siswas', function (Blueprint $table) {
            $table->string('email', 100)->nullable()->default(null)->change();
            $table->string('notelp_wali', 15)->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->string('kode_regis', 10)->change();
        });

        Schema::table('wali_siswas', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->string('notelp_wali')->nullable(false)->change();
        });
    }
};