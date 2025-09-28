<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\EscolaController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\UsuarioController;


Route::prefix('master')->name('master.')->group(function () {
    // Rota principal redireciona para lista de escolas
    Route::get('/', function () {
        return redirect()->route('master.escolas.index');
    });
    Route::resource('escolas', EscolaController::class)->except(['show']);
    Route::resource('roles', RoleController::class);
    Route::resource('usuarios', UsuarioController::class);

    // // Associações Escola Mãe ↔ Escola Filha
    // Associações (fora do resource)
    Route::get('escolas-associacoes', [EscolaController::class, 'associacoes'])
        ->name('escolas.associacoes');
    // Route::get('associacoes', [App\Http\Controllers\Master\EscolaController::class, 'associacoes'])
    //     ->name('escolas.associacoes');
    Route::post('associacoes', [App\Http\Controllers\Master\EscolaController::class, 'associarFilha'])
        ->name('escolas.associar');

    Route::get('escolas-associacoes2', [EscolaController::class, 'associacoes2'])
        ->name('escolas.associacoes2');

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