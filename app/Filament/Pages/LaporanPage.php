<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

/**
 * Page ini sudah digantikan oleh LaporanPendaftaran dan LaporanPembayaran.
 * Disembunyikan dari navigasi agar tidak muncul di sidebar.
 */
class LaporanPage extends Page
{
    protected string $view = 'filament.pages.laporan-pendaftaran';

    // Sembunyikan dari sidebar sepenuhnya
    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return false;
    }
}
