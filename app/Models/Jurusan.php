<?php

namespace App\Models;

use App\Traits\TenantSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jurusan extends Model
{
    use TenantSekolah;
    protected $table = 'jurusans';

    protected $fillable = [
        'sekolah_id',
        'nama_jurusan',
        'is_active',
    ];

    // ===================== RELASI =====================

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class, 'jurusan_id');
    }
}
