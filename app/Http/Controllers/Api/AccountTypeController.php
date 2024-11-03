<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AccountTypeController extends Controller
{
    public function show($id)
    {
        $user = User::findOrFail($id);
        return $user->wallets->pluck('account_type');
    }
}
