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
