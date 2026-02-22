<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Siswa extends Model
{
    protected $fillable =
        [
            'pendaftaran_id',
            'nisn',
            'nama_siswa',
            'jk',
            'phone',
            'email',
            'agama',
            'tempat_lahir',
            'tanggal_lahir',
            'asal_sekolah',
            'tahun_lulus',
            'nomor_ijazah',
        ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }
}
