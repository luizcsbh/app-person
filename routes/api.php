<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FotoPessoaController;
use App\Http\Controllers\UnidadeController;
use App\Http\Controllers\LotacaoController;
use App\Http\Controllers\ServidorEfetivoController;
use App\Http\Controllers\ServidorTemporarioController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Todas as rotas API são prefixadas com '/api' pelo RouteServiceProvider
| e protegidas por middlewares adequados.
|
*/

Route::prefix('v1')->group(function() {
    // Rotas Públicas
    Route::prefix('auth')->group(function() {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    // Rotas Protegidas
    Route::middleware(['auth:sanctum', 'api'])->group(function() {
        // Autenticação
        Route::prefix('auth')->group(function() {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('user', [AuthController::class, 'userProfile']);
            Route::post('refresh', [AuthController::class, 'refreshToken']);
        });

        // Recursos
        Route::apiResources([
            'unidades' => UnidadeController::class,
            'lotacoes' => LotacaoController::class,
            'servidores-efetivos' => ServidorEfetivoController::class,
            'servidores-temporarios' => ServidorTemporarioController::class,
        ]);

        // Rotas Customizadas
        Route::get('lotacoes/unidade/{unidade}/servidores', [LotacaoController::class, 'servidoresPorUnidade']);
        Route::get('servidores-efetivos/{servidor}/endereco-funcional', [ServidorEfetivoController::class, 'consultarEnderecoFuncional']);

        // Fotos
        Route::prefix('fotos-pessoas')->group(function() {
            Route::post('/', [FotoPessoaController::class, 'store']);
            Route::get('/{pessoa}', [FotoPessoaController::class, 'show']);
            Route::delete('/{pessoa}', [FotoPessoaController::class, 'destroy']);
        });
    });
});