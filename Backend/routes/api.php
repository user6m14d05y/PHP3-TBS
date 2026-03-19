<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CategoryController; // Import Controller Danh Mục

Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Welcome to the API!'
    ]);
});

// Auth & Contact routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/Login', [AuthController::class, 'Login']);
Route::post('/SubmitContact', [ContactController::class, 'SubmitContact']);
Route::middleware('auth:sanctum')->post('/Logout', [AuthController::class, 'Logout']);

// Category routes (Dùng /category như một tài nguyên chuẩn REST)
Route::get('/category', [CategoryController::class, 'index']); 
Route::post('/category', [CategoryController::class, 'store']); 
Route::post('/category/update/{id}', [CategoryController::class, 'update']); 
Route::delete('/category/{id}', [CategoryController::class, 'destroy']);


// User info
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Test & Debug routes
Route::get('/users', function () {
    $items = \Illuminate\Support\Facades\DB::table('users')->get();
    return response()->json([
        'status' => 'success',
        'data' => $items
    ]);
});

Route::get('/contact', function () {
    $contact = \Illuminate\Support\Facades\DB::table('contact')->get();
    return response()->json([
        'status' => 'success',
        'data' => $items
    ]);
});
