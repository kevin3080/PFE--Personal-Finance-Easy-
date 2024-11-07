<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccountType;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/users/wallets",
     *     summary="Obtener todas las carteras del usuario autenticado",
     *     tags={"Wallet"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de carteras del usuario autenticado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="wallets", type="array", @OA\Items(
     *                 @OA\Property(property="wallet_id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="account_type", type="string", example="Bancaria"),
     *                 @OA\Property(property="balance", type="number", example=1000),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-06T19:10:44Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-06T19:10:44Z"),
     *             )),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No hay carteras creadas en este usuario",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="No hay carteras creadas en este usuario")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error inesperado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="error inesperado")
     *         )
     *     )
     * )
     */
    public function show(Request $request)
    {
        try{
            $id = $request->user()->id;
            $wallets = Wallet::where('user_id', $id)->with('accountType')->get();
            $wallets = $wallets->map(function ($wallet) {
                return [
                    'wallet_id' => $wallet->id,
                    'user_id' => $wallet->user_id,
                    'account_type' => $wallet->accountType->type_account,
                    'balance' => $wallet->accountType->balance,
                    'created_at' => $wallet->created_at,
                    'updated_at' => $wallet->updated_at,
                ];
            });

            if ($wallets->isEmpty()) {
                return response()->json(['message' => 'No hay carteras creadas en este usuario'], 404);
            }

            return response()->json(['wallets' => $wallets], 200);
        }
        catch(Exception $e){
            return response()->json(['message' => 'error insesperado'. $e->getMessage()], 500);
        }
    }

      /**
     * @OA\Post(
     *     path="/api/v1/users/wallets/create",
     *     summary="Crear una nueva cartera para el usuario autenticado",
     *     tags={"Wallet"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="type_account", type="string", example="Bancaria"),
     *             @OA\Property(property="balance", type="number", example=5000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cartera creada exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="wallet creada correctamente", type="object",
     *                 @OA\Property(property="wallet_id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="account_type", type="string", example="Bancaria"),
     *                 @OA\Property(property="balance", type="number", example=5000),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-06T19:10:44Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-06T19:10:44Z"),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error inesperado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="error inesperado")
     *         )
     *     )
     * )
     */
    public function create(Request $request)
    {  
        try{
            $id = $request->user()->id;
            $accountType = AccountType::create([
                'type_account' => $request->type_account,
                'balance' => $request->balance
            ]);
            
            $wallet = Wallet::create([
                'user_id' => $id,
                'account_type_id' => $accountType->id,
            ]);
            
            $walletData = [
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'account_type' => $wallet->accountType->type_account,
                'balance' => $wallet->accountType->balance,
                'created_at' => $wallet->created_at,
                'updated_at' => $wallet->updated_at,
            ];
            

            return response()->json(['wallet creada correctamente' => $walletData], 201);
        }
        catch(Exception $e){
            return response()->json(['message' => 'error insesperado'. $e->getMessage()], 500);
        }
    }
}
