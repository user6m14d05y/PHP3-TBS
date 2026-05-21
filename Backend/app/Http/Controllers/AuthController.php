<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // ma hoa password

class AuthController extends Controller
{

    public function index(Request $request) {
        $limit = max(1, min($request->integer('limit', 20), 100));

        $users = User::query()
            ->select('id', 'name', 'email', 'role', 'created_at', 'updated_at')
            ->latest('id')
            ->paginate($limit);

        return response()->json([
            'status' => 'success',
            'data' => $users->items(),
            'total' => $users->total(),
            'per_page' => $users->perPage(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
        ]);
    }

    public function update(Request $request, $id) {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy người dùng!'
            ], 404);
        }

        // Block admin from self-changing role
        $loggedInUser = auth('sanctum')->user() ?: $request->user();
        if ($loggedInUser && (string)$user->id === (string)$loggedInUser->id) {
            if ($request->role && $request->role !== $user->role) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không được phép tự thay đổi vai trò (role) của chính mình!'
                ], 403);
            }
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

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