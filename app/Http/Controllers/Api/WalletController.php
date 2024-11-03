<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccountType;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function show($id)
    {
        try{
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

    public function create(Request $request, $id)
    {  
        try{
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
