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

        // 🔒 Corrige HTTPS atrás de proxy (Render/Cloudflare)
        Request::setTrustedProxies(
            ['0.0.0.0/0'], // Confia em todos os proxies
            Request::HEADER_X_FORWARDED_ALL
        );

        if (
            isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
            && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
        ) {
            URL::forceScheme('https');
            $_SERVER['HTTPS'] = 'on';
        }

        Carbon::setLocale('pt_BR');
        date_default_timezone_set('America/Sao_Paulo');
        Paginator::defaultView('vendor.pagination.default');

        if (app()->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
            $_SERVER['HTTPS'] = 'on';
        }

    }

}
