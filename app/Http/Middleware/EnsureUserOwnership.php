<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->route('id');

        // Verificar si el usuario autenticado coincide con el user_id
        if (Auth::id() !== (int) $userId) {
            return response()->json(['message' => 'Acceso no autorizado a este recurso'], 403);
        }

        return $next($request);
    }
}
