<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;

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
    /**
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Registrar um novo usuário",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", format="email", example="joao@email.com"),
     *             @OA\Property(property="password", type="string", example="12345678"),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Usuário registrado com sucesso"),
     *     @OA\Response(response=422, description="Dados inválidos")
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Usuário registrado com sucesso!'], 201);
    }

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
     *     @OA\Response(response=200, description="Login bem-sucedido"),
     *     @OA\Response(response=401, description="Credenciais inválidas")
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciais inválidas!'], 401);
        }

        // Criar token com expiração de 5 minutos
        $token = $user->createToken('auth_token')->plainTextToken;

        $user->tokens()->latest()->first()->update([
            'expires_at' => now()->addMinutes(10),
        ]);

        return response()->json([
            'message' => 'Login bem-sucedido!',
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => now()->addMinutes(5)->toDateTimeString(),
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
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso!'], 200);
    }

    /**
     * @OA\Get(
     *     path="/auth/user",
     *     summary="Obter informações do usuário autenticado",
     *     tags={"Autenticação"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Dados do usuário autenticado"),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function userProfile(Request $request)
    {
        return response()->json(['user' => $request->user()], 200);
    }
}
