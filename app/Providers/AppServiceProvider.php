<?php

namespace App\Providers;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Fix public path untuk Hostinger (public_html bukan public)
        $this->app->bind('path.public', function () {
            // Cek apakah ada public_html (Hostinger), fallback ke public
            $publicHtml = dirname(base_path()) . '/public_html';
            return is_dir($publicHtml) ? $publicHtml : base_path('public');
        });
    }

    public function boot(): void
    {
        // Carbon locale Indonesia
        Carbon::setLocale('id');
        CarbonImmutable::setLocale('id');

        // Pastikan storage/fonts ada untuk DomPDF
        $fontsDir = storage_path('fonts');
        if (!is_dir($fontsDir)) {
            @mkdir($fontsDir, 0755, true);
        }
    }
}
