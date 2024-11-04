<?php

namespace App\Http\Middleware;

use App\Models\Wallet;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWalletOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $walletId = $request->route('wallet_id');
        $wallet = Wallet::find($walletId);

        // Verifica que la cartera exista y pertenezca al usuario autenticado
        if (!$wallet || $wallet->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acceso no autorizado a esta cartera'], 403);
        }

        return $next($request);
    }
}
