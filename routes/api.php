<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return response()->json([
        'message' => 'Unauthenticated. Please provide a valid Bearer token.',
    ], 401);
})->name('login');

Route::prefix('v1')->group(function () {

    // ── Auth ─────────────────────────────────────────────────────────────────
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
    });

    // ── Products (guest) ──────────────────────────────────────────────────────
    Route::get('products',      [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);

    // ── Products (authenticated) ───────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('products',              [ProductController::class, 'store']);
        Route::put('products/{product}',     [ProductController::class, 'update']);
        Route::delete('products/{product}',  [ProductController::class, 'destroy']);
    });
});
