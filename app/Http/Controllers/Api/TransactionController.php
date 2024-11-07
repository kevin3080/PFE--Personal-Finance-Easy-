<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\Wallet;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/users/wallets/{wallet_id}/transaction",
     *     summary="Crear una transacción en la cartera del usuario",
     *     tags={"Transaction"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="wallet_id",
     *         in="path",
     *         required=true,
     *         description="ID de la cartera",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="type", type="string", example="ingreso"),
     *             @OA\Property(property="amount", type="number", example=100.50),
     *             @OA\Property(property="category", type="integer", example=3),
     *             @OA\Property(property="description", type="string", example="Pago de salario")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Transacción creada exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Transacción creada exitosamente"),
     *             @OA\Property(property="transaction", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="wallet_id", type="integer", example=1),
     *                 @OA\Property(property="transaction_type", type="string", example="ingreso"),
     *                 @OA\Property(property="category_id", type="integer", example=3),
     *                 @OA\Property(property="amount", type="number", example=100.50),
     *                 @OA\Property(property="balance_after_transaction", type="number", example=1100.50),
     *                 @OA\Property(property="date", type="string", format="date-time", example="2024-11-06T19:10:44Z"),
     *                 @OA\Property(property="description", type="string", example="Pago de salario")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Datos de transacción no válidos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Monto no válido")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error inesperado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Error inesperado")
     *         )
     *     )
     * )
     */
    public function index( Request $request, $wallet_id)
    {
        $wallet = Wallet::find($wallet_id);
        $accountType = $wallet->accountType;

        $transactionTypeInput = strtolower($request->input('type'));
        $transactionTypeId = $transactionTypeInput === 'ingreso' || $transactionTypeInput === 'income' ? 2 : 1;

        $transactionType = TransactionType::find($transactionTypeId);
        if (!$transactionType) {
            return response()->json(['message' => 'Tipo de transacción no válido'], 400);
        }

        $amount = $request->input('amount');
        if(!is_numeric($amount) || $amount <= 0){
            return response()->json(['message' => 'Monto no válido'], 400);
        }

        $categoryId = $request->input('category');

        DB::beginTransaction();

        try {
            // Crear la transacción
            $transaction = Transaction::create([
                'wallet_id' => $wallet_id,
                'transaction_type_id' => $transactionTypeId,
                'category_id' => $categoryId,
                'amount' => $amount,
                'date' => now(),
                'description' => $request->input('description'),
            ]);

            if ($transactionTypeId === 2) { // Ingreso
                $accountType->balance += $amount;
            } else if ($transactionTypeId === 1) { // Gasto
                $accountType->balance -= $amount;
            }

            // Guardar el balance actualizado
            $accountType->save();

            DB::commit();

            return response()->json([
                'message' => 'Transacción creada exitosamente',
                'transaction' => [
                    'id' => $transaction->id,
                    'wallet_id' => $transaction->wallet_id,
                    'transaction_type' => $transactionType->transaction_name,
                    'category_id' => $transaction->category_id,
                    'amount' => $transaction->amount,
                    'balance_after_transaction' => $accountType->balance,
                    'date' => $transaction->date,
                    'description' => $transaction->description,
                ]
            ], 201);

        } catch (Exception $e) {
            // Si ocurre algún error, revertir la transacción
            DB::rollBack();
            return response()->json(['message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/wallets/{wallet_id}/transactions",
     *     summary="Obtener todas las transacciones de una cartera específica",
     *     tags={"Transaction"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="wallet_id",
     *         in="path",
     *         required=true,
     *         description="ID de la cartera",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de transacciones de la cartera",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="wallet", type="string", example="Bancaria"),
     *             @OA\Property(property="transactions", type="array", @OA\Items(
     *                 @OA\Property(property="wallet_name", type="string", example="Bancaria"),
     *                 @OA\Property(property="type_transaccion", type="string", example="ingreso"),
     *                 @OA\Property(property="category", type="string", example="Salario"),
     *                 @OA\Property(property="ammount", type="string", example="$100.50"),
     *                 @OA\Property(property="date", type="string", format="date-time", example="2024-11-06T19:10:44Z")
     *             )),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cartera o transacciones no encontradas",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="No hay transacciones en esta cartera")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error inesperado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Error inesperado")
     *         )
     *     )
     * )
     */    
    public function show(Request $request, $wallet_id)
    {
        try{
            $wallet = Wallet::with(['accountType', 'transactions.transactionType', 'transactions.category'])
                ->findOrFail($wallet_id);
        }catch(ModelNotFoundException $e){
            return response()->json(['message' => 'Cartera no encontrada'], 404);
        }

        $transactions = $wallet->transactions->map(function ($transaction) use ($wallet) {
            return [
                'wallet_name' => $wallet->accountType->type_account,
                'type_transaccion' => $transaction->transactionType->transaction_name,
                'category' => optional($transaction->category)->name ?? 'Sin categoría',
                'ammount' => '$' . number_format($transaction->amount, 2),
                'date' => $transaction->date->format('Y-m-d H:i:s'),
            ];
        });

        if($transactions->isEmpty()){
            return response()->json([
                'wallet' => $wallet->accountType->type_account,
                'message' => 'No hay transacciones en esta cartera'
            ], 404);
        }

        return response()->json([
            'wallet' => $wallet->accountType->type_account,
            'transactions' => $transactions
        ], 200);
    }
}
