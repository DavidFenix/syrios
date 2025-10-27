<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; 
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;

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
        // ⚙️ Corrige erro de índice longo no MySQL (utf8mb4)
        Schema::defaultStringLength(191);

        // 📅 Localização e timezone
        Carbon::setLocale('pt_BR');
        date_default_timezone_set(config('app.timezone'));

        // 📄 Paginador customizado
        Paginator::defaultView('vendor.pagination.default');

        // 🌐 Força HTTPS apenas em produção (ex: Render)
        if (env('APP_ENV') === 'production' || env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }
    }
}
