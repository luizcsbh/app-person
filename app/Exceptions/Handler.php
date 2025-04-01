<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (LotacaoAtivaException $e, $request) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => ['lotacao' => [$e->getMessage()]]
            ], 422);
        });
    }

    public function render($request, Throwable $exception)
    {
        // Token expirado
        if ($exception instanceof \Laravel\Sanctum\Exceptions\MissingAbilityException) {
            return response()->json([
                'message' => 'Token expirado',
                'error' => 'token_expired'
            ], 401);
        }

        // Acesso não autorizado
        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return response()->json([
                'message' => 'Não autorizado',
                'error' => 'unauthenticated'
            ], 401);
        }

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? response()->json(['message' => 'Unauthenticated.'], 401)
            : redirect()->guest(route('auth.login')); // Altere para sua rota real
    }
}
