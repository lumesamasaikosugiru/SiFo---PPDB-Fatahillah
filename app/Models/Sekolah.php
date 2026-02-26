<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sekolah extends Model
{
    protected $table = 'sekolahs';

    protected $fillable = [
        'nama_sekolah',
        'tingkatan',
        'alamat',
        'kuota',
        'deskripsi',
        'logo',
    ];

    // ===================== RELASI =====================

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class, 'sekolah_id');
    }

    public function jurusans(): HasMany
    {
        return $this->hasMany(Jurusan::class, 'sekolah_id');
    }

    // Alias 'jurusan' untuk backward compat di views
    public function jurusan(): HasMany
    {
        return $this->hasMany(Jurusan::class, 'sekolah_id');
    }

    public function adminSekolahs(): HasMany
    {
        return $this->hasMany(AdminSekolah::class, 'sekolah_id');
    }
}
