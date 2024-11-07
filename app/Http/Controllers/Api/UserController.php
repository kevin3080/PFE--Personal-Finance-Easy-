<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     summary="Listar todos los usuarios",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de todos los usuarios",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return User::all();
    }

     /**
     * @OA\Get(
     *     path="/api/v1/users/{id}",
     *     summary="Obtener un usuario específico con sus carteras",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID del usuario"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Información del usuario y sus carteras",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="wallet", type="array", @OA\Items(ref="#/components/schemas/Wallet"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        $user_id = User::find($id);
        $wallet = Wallet::where('user_id', $id)->get();

        return response()->json(['user' => $user_id, 'wallet' => $wallet], 200);
    }
}
