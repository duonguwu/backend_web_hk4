<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Auth;

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

Auth::routes(['verify' => true]);

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/user', [AuthController::class, 'index']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
//Route::post('/login', [AuthController::class, 'login']);

Route::prefix('admin')->group(function () {
    Route::get('/getUser', [AuthController::class, 'index']);
    Route::get('/getInvoices', [CheckoutController::class, 'getAllInvoice']);
    Route::get('/getInvoices/{invoiceId}', [CheckoutController::class, 'getAllInvoiceDetails']);
    Route::post('/addproduct', [ProductController::class, 'addProduct']);
});
Route::prefix('user')->group(function () {
    Route::middleware(['checkCart'])->prefix('cart')->group(function () {
        Route::post('add', [CartController::class, 'addToCart']);
        Route::post('/update/{productId}', [CartController::class, 'updateProductQty']);
        Route::delete('/remove/{productId}', [CartController::class, 'removeFromCart']);
        Route::get('/get', [CartController::class, 'getCartItems']);
    });
    Route::middleware(['checkWishlist'])->prefix('wishlist')->group(function () {
        Route::post('add', [WishlistController::class, 'addToWishlist']);
        Route::delete('/remove/{productId}', [WishlistController::class, 'removeFromWishlist']);
        Route::get('/get', [WishlistController::class, 'getWishlistItems']);
    });
    Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder']);
    Route::get('/getInvoices', [CheckoutController::class, 'getUserInvoices']);
    Route::get('/getInvoices/{invoiceId}', [CheckoutController::class, 'getInvoiceDetails']);
    Route::post('/address', [AddressController::class, 'storeAddress']);
    Route::get('/address/get', [AddressController::class, 'getAddress']);
});
