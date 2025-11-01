<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // 🔧 Configurações gerais
        config(['session.same_site' => 'none']);
        Schema::defaultStringLength(191);
        Carbon::setLocale('pt_BR');
        date_default_timezone_set('America/Sao_Paulo');
        Paginator::defaultView('vendor.pagination.default');

        // 🌐 Confia em proxies (importante pro Railway e Render)
        Request::setTrustedProxies(['0.0.0.0/0'], Request::HEADER_X_FORWARDED_ALL);

        // 🚀 Força HTTPS em ambiente local e produção (Railway proxy)
        if (App::environment(['production', 'local'])) {
            URL::forceScheme('https');

            // Railway às vezes omite X-Forwarded-Port
            if (request()->header('x-forwarded-proto') === 'https') {
                request()->server->set('HTTPS', 'on');
                request()->server->set('SERVER_PORT', '443');
            }
        }
    }
}

/*
namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        config(['session.same_site' => 'none']);

        Schema::defaultStringLength(191);
        Carbon::setLocale('pt_BR');
        date_default_timezone_set('America/Sao_Paulo');
        Paginator::defaultView('vendor.pagination.default');

        // ✅ Confia em proxies — Render envia cabeçalhos corretos
        Request::setTrustedProxies(['0.0.0.0/0'], Request::HEADER_X_FORWARDED_ALL);

    }

}
*/