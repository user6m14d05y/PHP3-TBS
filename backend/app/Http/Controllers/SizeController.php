<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    /**
     * Lấy danh sách tất cả kích thước.
     */
    public function index()
    {
        $sizes = Size::all();
        return response()->json([
            'status' => 'success',
            'data'   => $sizes
        ]);
    }

    /**
     * Thêm kích thước mới.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $size = Size::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Bạn đã thêm kích thước thành công',
            'data'    => $size
        ], 201);
    }

    /**
     * Cập nhật kích thước theo ID.
     */
    public function update(Request $request, $id)
    {
        $size = Size::find($id);

        if (!$size) {
            return response()->json(['message' => 'Không tìm thấy kích thước để sửa'], 404);
        }

        $size->name = $request->name ?? $size->name;
        $size->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Bạn đã cập nhật kích thước thành công',
            'data'    => $size
        ]);
    }

    /**
     * Xóa kích thước theo ID.
     */
    public function destroy($id)
    {
        $size = Size::find($id);

        if (!$size) {
            return response()->json(['message' => 'Không tìm thấy kích thước để xóa'], 404);
        }

        $size->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Bạn đã xóa kích thước thành công',
        ]);
    }
}
