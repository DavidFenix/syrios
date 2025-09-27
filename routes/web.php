<?php

use App\Http\Controllers\MasterController;

Route::prefix('master')->group(function () {
    Route::get('/', [MasterController::class, 'index'])->name('master.index');
    Route::post('/escola', [MasterController::class, 'storeEscola'])->name('master.storeEscola');
    Route::delete('/escola/{id}', [MasterController::class, 'destroyEscola'])->name('master.destroyEscola');
    Route::post('/role', [MasterController::class, 'storeRole'])->name('master.storeRole');
    Route::post('/usuario', [MasterController::class, 'storeUsuario'])->name('master.storeUsuario');
    Route::delete('/usuario/{id}', [MasterController::class, 'destroyUsuario'])->name('master.destroyUsuario');
});
