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


// ADMIN

// User routers
Route::get('/user', [AuthController::class, 'index']);
Route::post('/user/update/{id}', [AuthController::class, 'update']);
Route::delete('/user/{id}', [AuthController::class, 'destroy']);

// Contact routers
Route::get('/contact', [ContactController::class, 'index']);


// Category routes
Route::get('/category', [CategoryController::class, 'index']); 
Route::post('/category', [CategoryController::class, 'store']); 
Route::post('/category/update/{id}', [CategoryController::class, 'update']); 
Route::delete('/category/{id}', [CategoryController::class, 'destroy']);

// Category Item routes 
Route::get('/category-item', [CategoryItemController::class, 'index']);
Route::post('/category-item', [CategoryItemController::class, 'store']);
Route::post('/category-item/update/{id}', [CategoryItemController::class, 'update']);
Route::delete('/category-item/{id}', [CategoryItemController::class, 'destroy']);

// Size routes
Route::get('/size', [SizeController::class, 'index']); 
Route::post('/size', [SizeController::class, 'store']); 
Route::post('/size/update/{id}', [SizeController::class, 'update']); 
Route::delete('/size/{id}', [SizeController::class, 'destroy']);

// Product routes
Route::get('/product', [ProductController::class, 'index']);
Route::get('/product/search', [ProductController::class, 'search']);
Route::get('/product/{slug}', [ProductController::class, 'show']);
Route::post('/product', [ProductController::class, 'store']);
Route::post('/product/update/{id}', [ProductController::class, 'update']);
Route::delete('/product/{id}', [ProductController::class, 'destroy']);
Route::delete('/product/image/{imageId}', [ProductController::class, 'destroyImage']);
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



// User info (get user login)
Route::get('/me', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
