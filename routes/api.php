<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::middleware(['auth:api'])->group(function () {
//     Route::prefix('user')->group(function () {
//         Route::middleware(['checkCart'])->prefix('cart')->group(function () {
//             Route::post('add', [CartController::class, 'addToCart']);
//             Route::put('/update/{cartItemId}', [CartController::class, 'updateCartItem']);
//             Route::delete('/remove/{cartItemId}', [CartController::class, 'removeFromCart']);
//             Route::get('/get', [CartController::class, 'getCartItems2']);
//         });
//     });
// });

//Route::middleware('auth:sanctum')->get('/user-info', [AuthController::class, 'getUserInfo']);
Route::get('/user-info', [AuthController::class, 'getUserInfo']);

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/user', [AuthController::class, 'index']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
//Route::post('/login', [AuthController::class, 'login']);


Route::prefix('user')->group(function () {
    Route::middleware(['checkCart'])->prefix('cart')->group(function () {
        Route::post('add', [CartController::class, 'addToCart']);
        Route::put('/update/{cartItemId}', [CartController::class, 'updateCartItem']);
        Route::delete('/remove/{cartItemId}', [CartController::class, 'removeFromCart']);
        Route::get('/get', [CartController::class, 'getCartItems2']);
    });
});
