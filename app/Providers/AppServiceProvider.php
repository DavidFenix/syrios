<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Migrations\Migrator;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Impede execução de migrações definindo um diretório vazio
        $this->app->afterResolving('migrator', function (Migrator $migrator) {
            $migrator->path('database/migrations_disabled');
        });

        // 🔧 Configurações gerais
        Schema::defaultStringLength(191);
        Carbon::setLocale('pt_BR');
        Paginator::defaultView('vendor.pagination.default');

        /*
        |--------------------------------------------------------------------------
        | Força HTTPS somente quando houver proxy indicando isso
        |--------------------------------------------------------------------------
        */
        if ($this->app->environment('production')) {
            if (request()->header('x-forwarded-proto') === 'https') {
                URL::forceScheme('https');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Criação automática do symlink storage → public/storage
        | Somente em produção e somente se ainda não existir
        |--------------------------------------------------------------------------
        */
        if ($this->app->environment('production')) {
            $public = public_path('storage');
            $target = storage_path('app/public');

            // Se o link ainda NÃO existir
            if (!is_link($public)) {
                try {
                    // garante que o diretório de destino existe
                    if (!is_dir($target)) {
                        @mkdir($target, 0755, true);
                    }

                    // cria o link
                    symlink($target, $public);
                } catch (\Throwable $e) {
                    // silencioso para não quebrar o sistema
                    // railway não permite mkdir em certas horas
                }
            }
        }
    }


    // public function boot()
    // {
    //     // 🔧 Configurações gerais
    //     Schema::defaultStringLength(191);
    //     Carbon::setLocale('pt_BR');
    //     //date_default_timezone_set('America/Sao_Paulo');
    //     Paginator::defaultView('vendor.pagination.default');

    //     // if (App::environment('production')) {
    //     //     URL::forceScheme('https');
    //     // }

    //     if ($this->app->environment('production')) {
    //         if (request()->header('x-forwarded-proto') === 'https') {
    //             URL::forceScheme('https');
    //         }
    //     }


    // }
}
