<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MetodePembayaran extends Model
{
    protected $fillable =
        [
            'nama_metode',
            'deskripsi',
            'is_active',
        ];

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'metode_pembayaran_id');
    }
}
