<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pendaftaran extends Model
{

    protected $fillable =
        [
            'kode_regis',
            'tahun_akademik_id',
            'sekolah_id',
            'jurusan_id',
            'jalur_pendaftaran',
            'status',
            'tanggal_submit',
            'dibuat_oleh',
        ];


    //ini relasi belongsto
    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    //hasone

    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class, 'pendaftaran_id');
    }

    public function waliSiswa(): HasOne
    {
        return $this->hasOne(WaliSiswa::class, 'pendaftaran_id');
    }

    //hasmany

    public function dokumens(): HasMany
    {
        return $this->HasMany(Dokumen::class, 'pendaftaran_id');
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'pendaftaran_id');
    }
}
