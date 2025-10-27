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

        if (env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        if (app()->environment('production')) {
            Request::setTrustedProxies(
                [Request::HEADER_X_FORWARDED_ALL],
                Request::HEADER_X_FORWARDED_PROTO
            );
        }

        date_default_timezone_set(config('app.timezone', 'America/Sao_Paulo'));
        Carbon::setLocale('pt_BR');
        Paginator::defaultView('vendor.pagination.default');
    }
}
