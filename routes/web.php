<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Master
use App\Http\Controllers\Master\EscolaController as MasterEscolaController;
use App\Http\Controllers\Master\RoleController as MasterRoleController;
use App\Http\Controllers\Master\UsuarioController as MasterUsuarioController;
use App\Http\Controllers\Master\DashboardController as MasterDashboardController;

// Secretaria
use App\Http\Controllers\Secretaria\EscolaController as SecretariaEscolaController;
use App\Http\Controllers\Secretaria\UsuarioController as SecretariaUsuarioController;

// Escola
use App\Http\Controllers\Escola\UsuarioController as EscolaUsuarioController;

/*
|--------------------------------------------------------------------------
| Rotas Públicas (sem login)
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Rotas do Master (apenas usuários com role=master)
|--------------------------------------------------------------------------
*/
Route::prefix('master')
    ->middleware(['auth', 'role:master'])
    ->name('master.')
    ->group(function () {

        // Dashboard principal do Master
        Route::get('dashboard', [MasterDashboardController::class, 'index'])
            ->name('dashboard');

        // Redireciona /master para o dashboard
        Route::get('/', fn () => redirect()->route('master.dashboard'));

        // CRUDs principais
        Route::resource('escolas', MasterEscolaController::class)->except(['show']);
        Route::resource('roles', MasterRoleController::class);
        Route::resource('usuarios', MasterUsuarioController::class);

        // Associações Escola Mãe ↔ Escola Filha
        Route::get('associacoes', [MasterEscolaController::class, 'associacoes'])
            ->name('escolas.associacoes');
        Route::post('associacoes', [MasterEscolaController::class, 'associarFilha'])
            ->name('escolas.associar');

    });

/*
|--------------------------------------------------------------------------
| Rotas da Secretaria (apenas usuários com role=secretaria)
|--------------------------------------------------------------------------
*/
Route::prefix('secretaria')
    ->middleware(['auth', 'role:secretaria'])
    ->name('secretaria.')
    ->group(function () {

        // Redireciona /secretaria para a lista de escolas filhas
        Route::get('/', fn () => redirect()->route('secretaria.escolas.index'))
            ->name('dashboard');

        // CRUDs da Secretaria
        Route::resource('escolas', SecretariaEscolaController::class)->except(['show']);
        Route::resource('usuarios', SecretariaUsuarioController::class)->except(['show']);
    });

/*
|--------------------------------------------------------------------------
| Rotas da Escola (apenas usuários com role=escola)
|--------------------------------------------------------------------------
*/
Route::prefix('escola')
    ->middleware(['auth', 'role:escola'])
    ->name('escola.')
    ->group(function () {

        // Redireciona /escola para a lista de usuários da escola
        Route::get('/', fn () => redirect()->route('escola.usuarios.index'));

        // CRUD de usuários da escola (professores, pais, etc.)
        Route::resource('usuarios', EscolaUsuarioController::class)->except(['show']);
    });
