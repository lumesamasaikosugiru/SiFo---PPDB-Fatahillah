<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAkademik extends Model
{
    protected $table = 'tahun_akademiks';

    protected $fillable = [
        'tahun',
        'is_active',
    ];

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class, 'tahun_akademik_id');
    }
}
