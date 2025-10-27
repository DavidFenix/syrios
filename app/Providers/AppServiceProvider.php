<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // 🔧 Compatibilidade MySQL
        Schema::defaultStringLength(191);

        // 🕒 Configurações de locale e timezone
        Carbon::setLocale('pt_BR');
        date_default_timezone_set('America/Sao_Paulo');
        Paginator::defaultView('vendor.pagination.default');

        // 🌐 Confia nos proxies (Render / Cloudflare / Heroku)
        // Isso permite que o Laravel reconheça o cabeçalho X-Forwarded-Proto: https
        Request::setTrustedProxies(
            ['0.0.0.0/0'], // confia em todos
            Request::HEADER_X_FORWARDED_ALL
        );

        // 🔒 Força HTTPS apenas quando realmente necessário
        if (
            app()->environment('production') ||
            env('FORCE_HTTPS', false) ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        ) {
            URL::forceScheme('https');
            $_SERVER['HTTPS'] = 'on';
        }
    }
}
