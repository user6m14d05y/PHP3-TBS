<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;

// Add this line to run the route: http://localhost:8000/api
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

// User info
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Test & Debug routes
Route::get('/users', function () {
    $users = \Illuminate\Support\Facades\DB::table('users')->get();
    return response()->json([
        'status' => 'success',
        'data' => $users
    ]);
});

Route::get('/contact', function () {
    $users = \Illuminate\Support\Facades\DB::table('contact')->get();
    return response()->json([
        'status' => 'success',
        'data' => $users
    ]);
});
