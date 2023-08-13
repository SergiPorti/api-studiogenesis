<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::post('/login', [AuthController::class, 'login'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'optional_token'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/update-password', [AuthController::class, 'updatePassword']);
    Route::post('/loginToken', [AuthController::class, 'loginByToken']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::post('/register', [AuthController::class, 'register']);
