<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

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
        
        Carbon::setLocale('pt_BR');
        date_default_timezone_set(config('app.timezone'));
    }
}
