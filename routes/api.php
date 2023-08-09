<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make somethinPg great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::post('/login', [AuthController::class, 'login'])->middleware('auth:sanctum');

Route::middleware('api_auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/update-password', [AuthController::class, 'updatePassword']);
});

Route::post('/register', [AuthController::class, 'register']);
