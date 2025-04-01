<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @OA\Tag(name="Autenticação")
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer"
 * )
 */
class AuthController extends Controller
{
    // ... (métodos register e userProfile permanecem iguais)

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Login do usuário",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="joao@email.com"),
     *             @OA\Property(property="password", type="string", example="12345678"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login bem-sucedido",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="expires_at", type="string", format="date-time"),
     *             @OA\Property(property="refresh_token", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Credenciais inválidas"),
     *     @OA\Response(response=403, description="Acesso não permitido para este domínio")
     * )
     */
    public function login(Request $request)
    {
        // Verificação de domínio permitido
        $allowedDomains = ['dominio-permitido.com', 'localhost'];
        $referer = parse_url($request->headers->get('referer'), PHP_URL_HOST);
        
        if (!in_array($referer, $allowedDomains) && !app()->environment('local')) {
            Log::warning("Tentativa de acesso não autorizada do domínio: $referer");
            return response()->json([
                'message' => 'Acesso não permitido para este domínio'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Erro de validação'
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        // Cria token de acesso com expiração de 5 minutos
        $token = $user->createToken(
            'auth_token',
            ['*'],
            now()->addMinutes(5)
        )->plainTextToken;

        // Cria token de refresh com expiração maior
        $refreshToken = $user->createToken(
            'refresh_token',
            ['refresh'],
            now()->addDays(7)
        )->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => now()->addMinutes(5)->toDateTimeString(),
            'refresh_token' => $refreshToken
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/auth/refresh",
     *     summary="Renovar token de acesso",
     *     tags={"Autenticação"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"refresh_token"},
     *             @OA\Property(property="refresh_token", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Token renovado com sucesso"),
     *     @OA\Response(response=401, description="Não autorizado"),
     *     @OA\Response(response=403, description="Token de refresh inválido")
     * )
     */
    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string'
        ]);

        // Busca o token no banco de dados
        $token = PersonalAccessToken::findToken($request->refresh_token);

        if (!$token || !$token->can('refresh')) {
            return response()->json([
                'message' => 'Token de refresh inválido'
            ], 403);
        }

        $user = $token->tokenable;

        // Revoga o token antigo
        $token->delete();

        // Cria novo token de acesso
        $newToken = $user->createToken(
            'auth_token',
            ['*'],
            now()->addMinutes(5)
        )->plainTextToken;

        // Cria novo token de refresh
        $newRefreshToken = $user->createToken(
            'refresh_token',
            ['refresh'],
            now()->addDays(7)
        )->plainTextToken;

        return response()->json([
            'token' => $newToken,
            'token_type' => 'Bearer',
            'expires_at' => now()->addMinutes(5)->toDateTimeString(),
            'refresh_token' => $newRefreshToken
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/auth/logout",
     *     summary="Fazer logout",
     *     tags={"Autenticação"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Logout bem-sucedido"),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'message' => 'Nenhum usuário autenticado'
                ], 401);
            }

            // Revoga todos os tokens do usuário
            $user->tokens()->delete();

            return response()->json([
                'message' => 'Logout realizado com sucesso'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erro durante logout: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao realizar logout'
            ], 500);
        }
    }
}