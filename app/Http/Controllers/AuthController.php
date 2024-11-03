<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function test()
    {
        return response()->json(['message' => 'Funciona'], 200);
    }
    
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

    // Inicio de sesión
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 200);
    }

    // Cierre de sesión
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        //$request->user()->currentAccessToken()->delete(); // para borrar el token actual, no elimina todas las sesiones
        return response()->json(['message' => 'Cierre de sesión exitoso'], 200);
    }

    // para mirar las sesiones activas del mismo usuario
    public function activeSessions(Request $request)
    {
        $user = $request->user();
        $tokens = $user->tokens;

        return response()->json([
            'message' => 'Sesiones activas',
            'tokens' => $tokens
        ], 200);
    }
}
