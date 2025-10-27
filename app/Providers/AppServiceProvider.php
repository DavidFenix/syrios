<?php

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
        Schema::defaultStringLength(191);
        Carbon::setLocale('pt_BR');
        date_default_timezone_set('America/Sao_Paulo');
        Paginator::defaultView('vendor.pagination.default');

        // ✅ Confia em proxies — Render envia cabeçalhos corretos
        Request::setTrustedProxies(['0.0.0.0/0'], Request::HEADER_X_FORWARDED_ALL);
    }
}
