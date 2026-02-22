<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaliSiswa extends Model
{
    protected $fillable =
        [
            'pendaftaran_id',
            'nama_wali',
            'hubungan',
            'pekerjaan',
            'notelp_wali',
            'email',
        ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }
}
