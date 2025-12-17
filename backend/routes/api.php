<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and assigned the "api"
| middleware group. Enjoy building your API!
|
*/

Route::get('/hello', function () {
    return response()->json(['message' => 'Hello API!']);
});
Route::get('/sanctum/csrf-cookie', [AuthController::class, 'csrfCookie']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])
      ->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class,'index']);
    Route::get('/orders', [OrderController::class,'index']);
    Route::post('/orders', [OrderController::class,'store']);
    Route::post('/orders/{id}/cancel', [OrderController::class,'cancel']);
});

