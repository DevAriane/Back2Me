<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ObjetController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ClaimController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\StatsController;
use Illuminate\Support\Facades\Route;

// Publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protégées par authentification
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/user/fcm-token', [AuthController::class, 'updateFcmToken']);

    // Objets (lecture et création pour tous)
    Route::apiResource('objets', ObjetController::class)->except(['update', 'destroy']);
    Route::post('/objets/{objet}/mark-returned', [ObjetController::class, 'markReturned'])->middleware('admin');
    Route::put('/objets/{objet}', [ObjetController::class, 'update'])->middleware('admin');
    Route::delete('/objets/{objet}', [ObjetController::class, 'destroy'])->middleware('admin');

    // Signalements
    Route::post('/objets/{objet}/claim', [ClaimController::class, 'store']);
    Route::get('/claims/pending', [ClaimController::class, 'pending'])->middleware('admin');
    Route::post('/claims/{claim}/approve', [ClaimController::class, 'approve'])->middleware('admin');
    Route::post('/claims/{claim}/reject', [ClaimController::class, 'reject'])->middleware('admin');

    // Catégories (lecture seule)
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

    // Statistiques (admin)
    Route::get('/stats', [StatsController::class, 'index'])->middleware('admin');

    // Gestion utilisateurs (admin)
    Route::apiResource('users', UserController::class)->middleware('admin');
});