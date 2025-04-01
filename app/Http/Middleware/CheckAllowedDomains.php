<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAllowedDomains
{
    public function handle(Request $request, Closure $next)
    {
        // Domínios permitidos
        $allowedDomains = ['dominio-permitido.com', 'localhost'];
        
        // Verifica o domínio de origem
        $origin = $request->headers->get('origin');
        $host = parse_url($origin, PHP_URL_HOST);
        
        // Permite requisições locais em desenvolvimento
        if (app()->environment('local')) {
            return $next($request);
        }

        // Verifica se o domínio está na lista de permitidos
        if (!in_array($host, $allowedDomains)) {
            return response()->json([
                'message' => 'Acesso não permitido para este domínio'
            ], 403);
        }

        return $next($request);
    }
}