<?php

namespace App\Models;

use App\Traits\TenantSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Siswa extends Model
{
    use TenantSekolah;
    protected $table = 'siswas';

    protected $fillable = [
        'pendaftaran_id',
        'nisn',
        'nama_siswa',
        'jk',           // 'Laki-Laki' | 'Perempuan'
        'phone',
        'email',
        'agama',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'asal_sekolah',
        'tahun_lulus',
        'nomor_ijazah',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // ===================== RELASI =====================

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }

    // ===================== ACCESSOR =====================

    // Alias jenis_kelamin -> jk untuk backward compat di views
    public function getJenisKelaminAttribute(): ?string
    {
        return $this->jk;
    }
}
