<?php

use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);

Route::middleware('auth:sanctum')->group( function () {
    Route::middleware('role:admin')->group(function () {
        Route::resource('products', ProductController::class);
    });

    Route::middleware('role:staff')->group(function () {
        Route::resource('products', ProductController::class)->only(['index', 'show', 'store']);
    });

    Route::middleware('role:guest')->group(function () {
        Route::resource('products', ProductController::class)->only(['index', 'show']);
    });
});
