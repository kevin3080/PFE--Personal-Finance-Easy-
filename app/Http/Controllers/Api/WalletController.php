<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccountType;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function show($id)
    {
        try{
            $wallets = Wallet::where('user_id', $id)->with('accountType')->get();
            $wallets = $wallets->map(function ($wallet) {
            return [
                'id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'account_type' => $wallet->accountType->type_account,
                'balance' => $wallet->accountType->balance,
                'created_at' => $wallet->created_at,
                'updated_at' => $wallet->updated_at,
            ];
        });

        return response()->json(['wallets' => $wallets], 200);

        }catch(ModelNotFoundException $e){
            return response()->json(['message' => 'No existen carteras'], 404);     
        }
        catch(Exception $e){
            return response()->json(['message' => 'error insesperado'. $e->getMessage()], 500);
        }
    }

    public function create(Request $request)
    {  
        try{
            $wallet = Wallet::create([
                'user_id' => $request->user_id,
                'balance' => $request->balance,
            ]);
            return $wallet;
        }catch(Exception $e){
            return response()->json(['message' => 'error insesperado'. $e->getMessage()], 500);
        }
        
    }
}
