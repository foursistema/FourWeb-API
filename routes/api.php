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
// PROXY DE LOGO PARA EXTRAÇÃO DE COR (CORS BYPASS)
Route::get('/proxy-logo', function (Illuminate\Http\Request $request) {
    $url = $request->query('url');
    if (!$url) return response()->json(['error' => 'No URL'], 400);
    try {
        $client = new \GuzzleHttp\Client();
        $res = $client->get($url);
        return response($res->getBody(), 200)
            ->header('Content-Type', $res->getHeaderLine('Content-Type'))
            ->header('Access-Control-Allow-Origin', '*');
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed'], 500);
    }
});
Route::get('/filtros', FiltrosController::class)->name('filtros');
