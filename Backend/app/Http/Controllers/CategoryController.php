<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::select('id', 'name', 'img')
            ->orderBy('id')
            ->get();
         return response()->json([
            'status' => 'success',
            'data'   => $category
        ]);
    }

    // API Thêm mới (POST)
    public function store(Request $request)
    {
        $category = new Category();
        $category->name = $request->name;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            
            $image->move(public_path('images'), $imageName);
            $category->img = $imageName; 
        } else {
            $category->img = $request->img; 
        }

        $category->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Bạn đã thêm danh mục thành công',
            'data' => $category
        ]);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Không tìm thấy danh mục để sửa'], 404);
        }

        $category->name = $request->name ?? $category->name;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName);
            
            if ($category->img && file_exists(public_path('images/' . $category->img))) {
                unlink(public_path('images/' . $category->img));
            }

            $category->img = $imageName;
        } elseif ($request->has('img')) {
            $category->img = $request->img;
        }

        $category->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Bạn đã cập nhật danh mục thành công',
            'data' => $category
        ]);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Không tìm thấy danh mục để xóa'], 404);
        }

        if ($category->img && file_exists(public_path('images/' . $category->img))) {
            unlink(public_path('images/' . $category->img));
        }

        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Bạn đã xoá danh mục thành công',
        ]);
    }
}
