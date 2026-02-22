<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sekolah extends Model
{
    protected $fillable =
        [
            'nama_sekolah',
            'tingkatan',
            'alamat',
            'kuota',
        ];

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class, 'sekolah_id');
    }

    public function jurusans(): HasMany
    {
        return $this->hasMany(Jurusan::class, 'sekolah_id');
    }

    public function adminSekolahs(): HasMany
    {
        return $this->hasMany(adminSekolah::class, 'sekolah_id');
    }
}
