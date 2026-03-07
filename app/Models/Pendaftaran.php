<?php

namespace App\Models;

use App\Traits\TenantSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pendaftaran extends Model
{
    use TenantSekolah;
    protected $table = 'pendaftarans';

    protected $fillable = [
        'kode_regis',
        'tahun_akademik_id',
        'sekolah_id',
        'jurusan_id',
        'jalur_pendaftaran',
        'ket_jalur_pendaftaran',
        'status',
        'diverifikasi_oleh',
        'tanggal_submit',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_submit' => 'datetime',
    ];

    // ===================== RELASI =====================

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }
    public function userVerifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class, 'pendaftaran_id');
    }

    public function waliSiswas(): HasMany
    {
        return $this->hasMany(WaliSiswa::class, 'pendaftaran_id');
    }

    public function dokumens(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'pendaftaran_id');
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'pendaftaran_id');
    }

    // Alias untuk backward-compat view lama yang pakai ->document
    public function document(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'pendaftaran_id');
    }

    // Helper: kode_registrasi alias -> kode_regis
    public function getKodeRegistrasiAttribute(): string
    {
        return $this->kode_regis ?? '';
    }

    // Helper: nama_siswa dari relasi siswa
    public function getNamaSiswaAttribute(): string
    {
        return $this->siswa->nama_siswa ?? '';
    }

    // Helper: asal_sekolah, tahun_lulus, nomor_ijazah dari siswa
    public function getAsalSekolahAttribute(): ?string
    {
        return $this->siswa->asal_sekolah ?? null;
    }

    public function getTahunLulusAttribute(): ?string
    {
        return $this->siswa->tahun_lulus ?? null;
    }

    public function getNomorIjazahAttribute(): ?string
    {
        return $this->siswa->nomor_ijazah ?? null;
    }
}
