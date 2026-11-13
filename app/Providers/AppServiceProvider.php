<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Zona waktu aplikasi = WIB (GMT+7), sumbernya config('app.timezone').
        // Laravel sudah menyetelnya saat bootstrap; diulang di sini supaya tetap
        // pasti walau php.ini server punya date.timezone lain.
        date_default_timezone_set(config('app.timezone', 'Asia/Jakarta'));

        // Nama hari & bulan Bahasa Indonesia untuk seluruh tampilan jam/tanggal
        // (Carbon::translatedFormat), mis. "Senin, 28 September 2026".
        Carbon::setLocale('id');

        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
    }
}
