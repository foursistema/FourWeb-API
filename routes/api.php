<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Dashboard\ConsultaController;
use App\Http\Controllers\Api\Dashboard\DespesasController;
use App\Http\Controllers\Api\Dashboard\ExtratoController;
use App\Http\Controllers\Api\Dashboard\FiltrosController;
use App\Http\Controllers\Api\Dashboard\GeralController;
use App\Http\Controllers\Api\Dashboard\ReceitasController;
use App\Http\Controllers\Api\EscolaController;
use Illuminate\Support\Facades\Route;

Route::get('/escola/lookup', [EscolaController::class, 'lookup'])->name('escola.lookup');
Route::get('/escola/{id}', [EscolaController::class, 'show'])
    ->whereNumber('id')
    ->name('escola.show');

Route::prefix('dashboard')->group(function (): void {
    Route::get('/geral', GeralController::class)->name('dashboard.geral');
    Route::get('/receitas', ReceitasController::class)->name('dashboard.receitas');
    Route::get('/despesas', DespesasController::class)->name('dashboard.despesas');
    Route::get('/consulta', ConsultaController::class)->name('dashboard.consulta');
    Route::get('/extrato', ExtratoController::class)->name('dashboard.extrato');
});

Route::get('/filtros', FiltrosController::class)->name('filtros');
