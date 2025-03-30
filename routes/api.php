<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnidadeController;
use App\Http\Controllers\LotacaoController;
use App\Http\Controllers\ServidorEfetivoController;
use App\Http\Controllers\ServidorTemporarioController;

/*----------------------------------------------------------
| Rotas Públicas (sem autenticação)
|----------------------------------------------------------*/
Route::prefix('auth')->group(function() {
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

/*----------------------------------------------------------
| Rotas Protegidas (com prefixo /api e autenticação Sanctum)
|----------------------------------------------------------*/
Route::middleware(['auth:sanctum', 'expire.token'])->group(function() {
    // Rotas de autenticação
    Route::prefix('auth')->group(function() {
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/user', [AuthController::class, 'userProfile'])->name('auth.user');
    });
    
    // Rotas de recursos
    Route::apiResource('unidades', UnidadeController::class);
    Route::apiResource('lotacoes', LotacaoController::class);
    Route::apiResource('servidores-efetivos', ServidorEfetivoController::class);
    Route::apiResource('servidores-temporarios', ServidorTemporarioController::class);
    
    // Rotas customizadas
    Route::get('lotacoes/unidade/{unid_id}/servidores', [LotacaoController::class, 'servidoresPorUnidade'])
        ->name('lotacoes.servidores-por-unidade');
});

/*----------------------------------------------------------
| Rotas Públicas Opcionais (se necessário)
|----------------------------------------------------------*/
// Caso precise de algumas rotas públicas para consulta:
// Route::get('/public/unidades', [UnidadeController::class, 'index'])->name('public.unidades.index');
// Route::get('/public/unidades/{id}', [UnidadeController::class, 'show'])->name('public.unidades.show');