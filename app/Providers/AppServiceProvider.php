<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; 
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // 🔒 Corrige HTTPS atrás de proxy (Render, Cloudflare)
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
            $_SERVER['HTTPS'] = 'on';
        }

        // 🔧 Confia nos proxies para detectar HTTPS corretamente
        Request::setTrustedProxies(
            ['0.0.0.0/0'], // confia em todos os proxies (Render usa IPs dinâmicos)
            Request::HEADER_X_FORWARDED_ALL
        );

        Carbon::setLocale('pt_BR');
        date_default_timezone_set('America/Sao_Paulo');
        Paginator::defaultView('vendor.pagination.default');
    }
}
