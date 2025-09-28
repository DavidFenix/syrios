<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\EscolaController;

Route::get('/', function () {
    return redirect('/master/escolas');
});

Route::prefix('master')->name('master.')->group(function () {
    // Rota principal redireciona para lista de escolas
    Route::get('/', function () {
        return redirect()->route('master.escolas.index');
    });

    // CRUD automático para Escola
    Route::resource('escolas', EscolaController::class);
});




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