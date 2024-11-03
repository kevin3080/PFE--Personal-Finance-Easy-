<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function show($id)
    {
        $user_id = User::find($id);
        $wallet = Wallet::where('user_id', $id)->get();

        return response()->json(['user' => $user_id, 'wallet' => $wallet], 200);
    }
}
