<?php

namespace App\Traits;

use Illuminate\Database\Console\Migrations\StatusCommand;
use illuminate\Database\Eloquent\Builder;

trait TenantSekolah
{
    public static function scopeSekolah(Builder $query): Builder
    {
        $user = auth()->user();

        if (!$user) {
            return $query;
        }

        if ($user->hasAnyRole('superadmin', 'admin_yayasan')) {
            return $query;
        }

        if ($user->adminSekolah?->sekolah_id) {
            return $query->where(
                'sekolah_id',
                $user->adminSekolah->sekolah_id
            );
        }

        return $query;
    }


}