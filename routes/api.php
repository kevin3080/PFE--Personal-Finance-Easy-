<?php

use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\EnsureUserOwnership;
use App\Http\Middleware\EnsureWalletOwner;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Rutas de autenticación
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    Route::get('/test', [AuthController::class, 'test']);

    // Rutas protegidas
    Route::get('/active-sessions', [AuthController::class, 'activeSessions'])->middleware('auth:sanctum');
    Route::get('/users', [UserController::class, 'index'])->middleware('auth:sanctum');
    Route::middleware(['auth:sanctum', EnsureUserOwnership::class])->group(function () {
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::get('/users/{id}/wallets', [WalletController::class, 'show']);
        Route::post('/users/{id}/wallets/create', [WalletController::class, 'create']);
        Route::middleware([EnsureWalletOwner::class])->group(function () {
            Route::get('/users/{id}/wallets/{wallet_id}/transactions', [TransactionController::class, 'show']);
            Route::post('/users/{id}/wallets/{wallet_id}/transaction', [TransactionController::class, 'index']);
        });
    });
});


