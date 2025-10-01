<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\EscolaController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\UsuarioController;
use App\Http\Controllers\Master\DashboardController;
use App\Http\Controllers\Secretaria\EscolaController as SecretariaEscolaController;
//use App\Http\Controllers\Secretaria\SecretariaController;
//use App\Http\Controllers\Secretaria\UsuarioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\LoginController;


Route::prefix('secretaria')->name('secretaria.')->middleware('auth')->group(function () {
    
    // Dashboard da secretaria (se quiser criar depois)
    Route::get('/', function () {
        return redirect()->route('secretaria.escolas.index');
    })->name('dashboard');

    // CRUD das escolas filhas da secretaria logada
    Route::resource('escolas', SecretariaEscolaController::class)->except(['show']);
    Route::resource('usuarios', App\Http\Controllers\Secretaria\UsuarioController::class);

    // Route::get('dashboard', [App\Http\Controllers\Secretaria\SecretariaController::class, 'dashboard'])->name('dashboard');

    Route::resource('secretarias', App\Http\Controllers\Secretaria\SecretariaController::class)->only(['index']);
    Route::resource('escolas', App\Http\Controllers\Secretaria\EscolaController::class)->only(['index']);
    Route::resource('usuarios', App\Http\Controllers\Secretaria\UsuarioController::class)->only(['index']);
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protege as rotas master atrás do middleware auth
Route::prefix('master')->middleware('auth')->name('master.')->group(function () {

    // Dashboard unificado
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Rota principal redireciona para dashboard
    Route::get('/', function () {
        return redirect()->route('master.dashboard');
    });

    Route::resource('escolas', EscolaController::class)->except(['show']);
    Route::resource('roles', RoleController::class);
    Route::resource('usuarios', UsuarioController::class);

    //passo 1: tudo começa quando alguem digita(faz get) ../master/escolas-associacoes2
    //Esta rota get vai usar a função associacoes2() definida na classe EscolaControler.
    Route::get('escolas-associacoes2', [EscolaController::class, 'associacoes2'])
         ->name('escolas.associacoes2');

    // CRUD de Associações Escola Mãe ↔ Escola Filha
    Route::get('associacoes', [EscolaController::class, 'associacoes'])
        ->name('escolas.associacoes');
    Route::post('associacoes', [EscolaController::class, 'associarFilha'])
        ->name('escolas.associar');

    

});

/*
Route::get('/', function () {
    return redirect('/master/escolas');
});

Route::prefix('master')->name('master.')->group(function () {
    // Rota principal redireciona para lista de escolas
    Route::get('/', function () {
        return redirect()->route('master.escolas.index');
    });

    // CRUD de escolas (Master)
    Route::resource('escolas', EscolaController::class);

    // CRUD de roles (Master)
    Route::resource('roles', \App\Http\Controllers\Master\RoleController::class);

    // CRUD de usuários (Master)
    Route::resource('usuarios', \App\Http\Controllers\Master\UsuarioController::class);

});
*/





/*
use App\Http\Controllers\MasterController;

Route::prefix('master')->group(function () {
    Route::get('/', [MasterController::class, 'index'])->name('master.index');
    Route::post('/escola', [MasterController::class, 'storeEscola'])->name('master.storeEscola');
    Route::delete('/escola/{id}', [MasterController::class, 'destroyEscola'])->name('master.destroyEscola');
    Route::post('/role', [MasterController::class, 'storeRole'])->name('master.storeRole');
    Route::post('/usuario', [MasterController::class, 'storeUsuario'])->name('master.storeUsuario');
    Route::delete('/usuario/{id}', [MasterController::class, 'destroyUsuario'])->name('master.destroyUsuario');
});

*/