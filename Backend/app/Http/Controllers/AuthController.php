<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // ma hoa password

class AuthController extends Controller
{

    public function index() {
        $users = User::all();
         return response()->json([
            'status' => 'success',
            'data'   => $users
        ]);
    }

    public function update(Request $request) {
        $user = User::find($request->id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật tài khoản thành công!'
        ], 200);
    }

    public function destroy($id) {
        $user = User::find($id);
        $user->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Xóa tài khoản thành công!'
        ], 200);
    }

    public function register(Request $request)
    {
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'status' => 'error',
                'errors' => [
                    'email' => ['Địa chỉ email này đã được sử dụng!']
                ]
            ], 422); 
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
            'role' => 'user'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Đăng ký tài khoản thành công!'
        ], 201); 
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'message' => 'Đăng nhập thành công!',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role 
                ]
            ], 200);
        }
        
        return response()->json([
            'status' => 'error',
            'message' => 'Email hoặc mật khẩu không chính xác!'
        ], 422);
    }

    public function Logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete(); 
        return response()->json([
            'status' => 'success',
            'message' => 'Đã đăng xuất thành công!'
        ]);
    }
}