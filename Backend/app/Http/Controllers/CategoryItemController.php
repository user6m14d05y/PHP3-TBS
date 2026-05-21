<?php

namespace App\Http\Controllers;

use App\Models\CategoryItem;
use Illuminate\Http\Request;

class CategoryItemController extends Controller
{
    /**
     * Lấy danh sách danh mục con (theo category_id nếu có truyền vào).
     * GET /api/category-item?category_id=1
     */
    public function index(Request $request)
    {
        if ($request->has('category_id')) {
            $items = CategoryItem::select('id', 'category_id', 'name')
                ->where('category_id', $request->category_id)
                ->orderBy('id')
                ->get();
        } else {
            // Lấy tất cả, kèm thông tin danh mục cha luôn
            $items = CategoryItem::select('id', 'category_id', 'name')
                ->with('category:id,name,img')
                ->orderBy('category_id')
                ->get();
        }
         return response()->json([
            'status' => 'success',
            'data'   => $items,
        ]);
    }

    /**
     * Thêm danh mục con mới.
     * POST /api/category-item
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|integer|exists:category,id',
            'name'        => 'required|string|max:50',
        ]);

        $item = CategoryItem::create([
            'category_id' => $request->category_id,
            'name'        => $request->name,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã thêm danh mục con thành công',
            'data'    => $item,
        ], 201);
    }

    /**
     * Cập nhật danh mục con.
     * POST /api/category-item/update/{id}
     */
    public function update(Request $request, $id)
    {
        $item = CategoryItem::find($id);

        if (!$item) {
            return response()->json(['message' => 'Không tìm thấy danh mục con'], 404);
        }

        $item->category_id = $request->category_id ?? $item->category_id;
        $item->name        = $request->name        ?? $item->name;
        $item->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã cập nhật danh mục con thành công',
            'data'    => $item,
        ]);
    }

    /**
     * Xoá danh mục con.
     * DELETE /api/category-item/{id}
     */
    public function destroy($id)
    {
        $item = CategoryItem::find($id);

        if (!$item) {
            return response()->json(['message' => 'Không tìm thấy danh mục con'], 404);
        }

        $item->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã xoá danh mục con thành công',
        ]);
    }
}
