<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaliSiswa extends Model
{
    protected $table = 'wali_siswas';

    protected $fillable = [
        'pendaftaran_id',
        'nama_wali',
        'hubungan',      // 'bapak' | 'ibu' | 'saudara_kandung' | 'saudara_keluarga'
        'pekerjaan',
        'notelp_wali',
        'email',
    ];

    // ===================== RELASI =====================

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }
}
