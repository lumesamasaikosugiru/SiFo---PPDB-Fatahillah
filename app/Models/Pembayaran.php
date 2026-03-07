<?php

namespace App\Models;

use App\Traits\TenantSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use TenantSekolah;
    protected $fillable = [
        'metode_pembayaran_id',
        'pendaftaran_id',
        'nominal',
        'order_id',
        'snap_token',
        'status_pembayaran',
        'tanggal_pembayaran',
        'proof_path',
        'verifikasi_oleh',
        'verifikasi_tanggal',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'verifikasi_tanggal' => 'datetime',
        'nominal' => 'integer',
    ];

    // ===================== RELASI =====================

    public function metodePembayaran(): BelongsTo
    {
        return $this->belongsTo(MetodePembayaran::class, 'metode_pembayaran_id');
    }

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikasi_oleh');
    }

    // ===================== HELPERS =====================

    public function getLabelStatusAttribute(): string
    {
        return match ($this->status_pembayaran) {
            'pending' => 'Menunggu Pembayaran',
            'menunggu_verifikasi' => 'Menunggu Verifikasi Admin',
            'sukses' => 'Pembayaran Lunas ✓',
            'gagal' => 'Pembayaran Gagal',
            'kadaluarsa' => 'Kadaluarsa',
            default => ucfirst($this->status_pembayaran),
        };
    }

    public function getNominalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    public function isPending(): bool
    {
        return $this->status_pembayaran === 'pending';
    }

    public function isMenungguVerifikasi(): bool
    {
        return $this->status_pembayaran === 'menunggu_verifikasi';
    }

    public function isSukses(): bool
    {
        return $this->status_pembayaran === 'sukses';
    }
}
