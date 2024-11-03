<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function show($id, $wallet_id)
    {
        $wallet = Wallet::where('id', $wallet_id)->where('user_id', $id)->firstOrFail();
        return $wallet->transactions;
    }
}
