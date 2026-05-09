<?php

namespace App\Models;

use App\Traits\TenantSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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

    // ===================== RELASI hasManyThrough agar bisa ambil data di tabel ini =====================
    public function waliSiswas(): HasManyThrough
    {
        return $this->hasManyThrough(
            WaliSiswa::class,
            Pendaftaran::class,
            'id',
            'pendaftaran_id',
            'pendaftaran_id',
            'id',
        );
    }
    public function dokumens(): HasManyThrough
    {
        return $this->hasManyThrough(
            Dokumen::class,
            Pendaftaran::class,
            'id',
            'pendaftaran_id',
            'pendaftaran_id',
            'id',
        );
    }

    // ===================== ACCESSOR =====================

    // Alias jenis_kelamin -> jk untuk backward compat di views
    public function getJenisKelaminAttribute(): ?string
    {
        return $this->jk;
    }
}
