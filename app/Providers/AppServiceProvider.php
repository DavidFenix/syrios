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
        Schema::defaultStringLength(191);
        Carbon::setLocale('pt_BR');
        //date_default_timezone_set('America/Sao_Paulo');
        Paginator::defaultView('vendor.pagination.default');

        // if (App::environment('production')) {
        //     URL::forceScheme('https');
        // }

        if ($this->app->environment('production')) {
            if (request()->header('x-forwarded-proto') === 'https') {
                URL::forceScheme('https');
            }
        }


    }
}
