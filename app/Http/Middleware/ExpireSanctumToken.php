<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExpireSanctumToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
    
        $token = $request->user()?->currentAccessToken();

        if ($token && $token->expires_at && now()->greaterThanOrEqualTo($token->expires_at)) {
            $token->delete(); // Revoga o token se estiver expirado
            return response()->json([
                'message' => 'Token expirado. Faça login novamente.'
            ], Response::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }
}
