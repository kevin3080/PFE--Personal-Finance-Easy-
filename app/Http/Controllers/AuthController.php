<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/register",
     *     summary="Registro de usuario",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="Juan Perez"),
     *             @OA\Property(property="email", type="string", format="email", example="juan@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuario registrado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuario registrado exitosamente"),
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *             @OA\Property(property="token", type="string", example="token_generado")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Error de validación")
     * )
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            //'telef' => $request->telef,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['message' => 'Usuario registrado exitosamente', 'user' => $user, 'token' => $token], 201);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/test",
     *     summary="Endpoint de prueba",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="Devuelve un mensaje de prueba"
     *     )
     * )
     */
    public function test()
    {
        return response()->json(['message' => 'Funciona'], 200);
    }
    
    
    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="Inicio de sesión",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="example@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inicio de sesión exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="token_generado"),
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Credenciales incorrectas")
     * )
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 200);
    }

    
    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="Cierre de sesión",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Cierre de sesión exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cierre de sesión exitoso")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        //$request->user()->currentAccessToken()->delete(); // para borrar el token actual, no elimina todas las sesiones
        return response()->json(['message' => 'Cierre de sesión exitoso'], 200);
    }

    /**
     * @OA\Schema(
     *     schema="Token",
     *     type="object",
     *     description="Token de autenticación",
     *     @OA\Property(property="access_token", type="string", description="Token de acceso"),
     *     @OA\Property(property="token_type", type="string", description="Tipo de token (por ejemplo, Bearer)"),
     *     @OA\Property(property="expires_in", type="integer", description="Tiempo de expiración en segundos"),
     *     @OA\Property(property="refresh_token", type="string", description="Token de refresco", nullable=true)
     * )
     */
    public function activeSessions(Request $request)
    {
        /**
         * @OA\Get(
         *     path="/auth/active-sessions",
         *     summary="Obtener sesiones activas",
         *     description="Devuelve una lista de sesiones activas con tokens de acceso",
         *     @OA\Response(
         *         response=200,
         *         description="Lista de sesiones activas",
         *         @OA\JsonContent(
         *             type="array",
         *             @OA\Items(ref="#/components/schemas/Token")
         *         )
         *     )
         * )
         */
        $user = $request->user();
        $tokens = $user->tokens;

        return response()->json([
            'message' => 'Sesiones activas',
            'tokens' => $tokens
        ], 200);
    }
}
