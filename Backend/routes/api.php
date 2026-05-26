<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CategoryController; 
use App\Http\Controllers\CategoryItemController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserAddressController;

Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Welcome to the API!'
    ]);
});

// USER

// Auth & Contact routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/Login', [AuthController::class, 'login']);
Route::post('/SubmitContact', [ContactController::class, 'SubmitContact']);
Route::post('/chatbot', [ChatbotController::class, 'ask']);
Route::middleware('auth:sanctum')->post('/Logout', [AuthController::class, 'Logout']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/items', [CartController::class, 'store']);
    Route::patch('/cart/items/{item}', [CartController::class, 'update']);
    Route::delete('/cart/items/{item}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);

    Route::get('/addresses', [UserAddressController::class, 'index']);
    Route::post('/addresses', [UserAddressController::class, 'store']);
    Route::get('/addresses/{address}', [UserAddressController::class, 'show']);
    Route::patch('/addresses/{address}', [UserAddressController::class, 'update']);
    Route::delete('/addresses/{address}', [UserAddressController::class, 'destroy']);
    Route::post('/addresses/{address}/default', [UserAddressController::class, 'makeDefault']);

    Route::post('/coupons/apply', [CouponController::class, 'apply']);
    Route::post('/checkout', [CheckoutController::class, 'store']);
});


Route::get('/category', [CategoryController::class, 'index']); 

// Category Item routes 
Route::get('/category-item', [CategoryItemController::class, 'index']);

// Size routes
Route::get('/size', [SizeController::class, 'index']); 

// Shop routes
Route::get('/shops', [ShopController::class, 'index']);
Route::post('/shops/delivery-check', [ShopController::class, 'deliveryCheck']);
Route::get('/shops/{shop}', [ShopController::class, 'show']);
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/shops', [ShopController::class, 'store']);
    Route::patch('/shops/{shop}', [ShopController::class, 'update']);
    Route::delete('/shops/{shop}', [ShopController::class, 'destroy']);
});

// Coupon admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/coupons', [CouponController::class, 'index']);
    Route::post('/coupons', [CouponController::class, 'store']);
    Route::get('/coupons/{coupon}', [CouponController::class, 'show']);
    Route::patch('/coupons/{coupon}', [CouponController::class, 'update']);
    Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy']);
});

// Product routes
Route::get('/product', [ProductController::class, 'index']);
Route::get('/product/search', [ProductController::class, 'search']);
Route::get('/product/{slug}', [ProductController::class, 'show']);
Route::get('/seo/products', function () {
    return response()->json(
        \App\Models\Product::query()
            ->select('id', 'name', 'slug', 'description', 'seo_title', 'meta_description', 'image_alt', 'thumbnail', 'category_id', 'category_item_id', 'is_active', 'created_at', 'updated_at')
            ->with([
                'category:id,name',
                'categoryItem:id,category_id,name',
                'variants:id,product_id,price,sale_price,stock,sku,is_active',
                'images:id,product_id,image_path,is_main,sort_order',
            ])
            ->where('is_active', true)
            ->orderBy('id')
            ->get()
    );
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // User routers
    Route::get('/user', [AuthController::class, 'index']);
    Route::post('/user/update/{id}', [AuthController::class, 'update']);
    Route::delete('/user/{id}', [AuthController::class, 'destroy']);

    // Contact routers
    Route::get('/contact', [ContactController::class, 'index']);

    // Category routes
    Route::post('/category', [CategoryController::class, 'store']);
    Route::post('/category/update/{id}', [CategoryController::class, 'update']);
    Route::delete('/category/{id}', [CategoryController::class, 'destroy']);

    // Category Item routes
    Route::post('/category-item', [CategoryItemController::class, 'store']);
    Route::post('/category-item/update/{id}', [CategoryItemController::class, 'update']);
    Route::delete('/category-item/{id}', [CategoryItemController::class, 'destroy']);

    // Size routes
    Route::post('/size', [SizeController::class, 'store']);
    Route::post('/size/update/{id}', [SizeController::class, 'update']);
    Route::delete('/size/{id}', [SizeController::class, 'destroy']);

    // Product routes
    Route::post('/product', [ProductController::class, 'store']);
    Route::post('/product/update/{id}', [ProductController::class, 'update']);
    Route::delete('/product/{id}', [ProductController::class, 'destroy']);
    Route::delete('/product/image/{imageId}', [ProductController::class, 'destroyImage']);

    // Payment status routes. Replace these with signed payment webhooks before production.
    Route::post('/orders/{order}/payment-succeeded', [CheckoutController::class, 'paymentSucceeded']);
    Route::post('/orders/{order}/payment-failed', [CheckoutController::class, 'paymentFailed']);
});



// User info (get user login)
Route::get('/me', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
