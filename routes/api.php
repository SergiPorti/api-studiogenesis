<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [UserController::class, 'me']);
    Route::post('/update-password', [AuthController::class, 'updatePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/update', [UserController::class, 'update']);

    Route::prefix('tickets')->group(function () {
        Route::get('/', [TicketController::class, 'getTickets']);
        Route::post('/create', [TicketController::class, 'create']);
        Route::post('/update', [TicketController::class, 'update']);
        Route::delete('/delete', [TicketController::class, 'delete']);
        Route::get('/search', [TicketController::class, 'search']);
    });
});
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
