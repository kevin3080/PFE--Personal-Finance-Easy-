<?php

use App\Http\Controllers\Api\AccountTypeController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Rutas de autenticación
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    Route::get('/test', [AuthController::class, 'test']);

    // Rutas protegidas
    Route::middleware('auth:sanctum')->get('/active-sessions', [AuthController::class, 'activeSessions']);
    Route::get('/users', [UserController::class, 'index'])->middleware('auth:sanctum');
    Route::get('/users/{id}', [UserController::class, 'show'])->middleware('auth:sanctum');
    Route::get('/users/{id}/wallets', [WalletController::class, 'show'])->middleware('auth:sanctum');
    Route::post('/users/{id}/wallets/create', [WalletController::class, 'create'])->middleware('auth:sanctum');
    Route::get('/users/{id}/wallets/{wallet_id}/transactions', [TransactionController::class, 'show'])->middleware('auth:sanctum');
});


