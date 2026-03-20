<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // GET /api/product — Lấy tất cả sản phẩm kèm variants và images
    public function index(Request $request)
    {
        $query = Product::with(['category', 'categoryItem', 'variants.size', 'images'])
            ->where('is_active', 1);

        // Lọc theo danh mục cha
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Lọc theo danh mục con
        if ($request->has('category_item_id')) {
            $query->where('category_item_id', $request->category_item_id);
        }

        $query->orderBy('created_at', 'desc');

        // Phân trang
        $limit = $request->get('limit', 12);
        $products = $query->paginate($limit);

        return response()->json([
            'status' => 'success',
            'data'   => $products->items(),
            'total'  => $products->total(),
            'per_page' => $products->perPage(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
        ]);
    }


    // GET /api/product/{id} — Lấy 1 sản phẩm
    public function show($id)
    {
        $product = Product::with(['category', 'categoryItem', 'variants.size', 'images'])->find($id);

        if (!$product) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $product]);
    }

    // POST /api/product — Thêm sản phẩm mới
    public function store(Request $request)
    {
        // 1. Tạo sản phẩm
        $product = new Product();
        $product->name             = $request->name;
        $product->slug             = Str::slug($request->name) . '-' . time();
        $product->description      = $request->description;
        $product->category_id      = $request->category_id;
        $product->category_item_id = $request->category_item_id;
        $product->is_active        = $request->is_active ?? true;

        // Ảnh đại diện (thumbnail)
        if ($request->hasFile('thumbnail')) {
            $file     = $request->file('thumbnail');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $fileName);
            $product->thumbnail = $fileName;
        }

        $product->save();

        // 2. Lưu nhiều ảnh gallery
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $index => $img) {
                $imgName = time() . '_' . $index . '_' . $img->getClientOriginalName();
                $img->move(public_path('images'), $imgName);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imgName,
                    'is_main'    => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }


        // 3. Lưu các biến thể (variants)
        if ($request->has('variants')) {
            $variants = is_string($request->variants)
                ? json_decode($request->variants, true)
                : $request->variants;

            foreach ($variants as $v) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size_id'    => $v['size_id'] ?? null,
                    'price'      => $v['price'],
                    'sale_price' => $v['sale_price'] ?? null,
                    'stock'      => $v['stock'] ?? 0,
                    'sku'        => $v['sku'] ?? null,
                    'is_active'  => true,
                ]);
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Thêm sản phẩm thành công',
            'data'    => $product->load(['variants.size', 'images'])
        ]);
    }

    // POST /api/product/update/{id} — Cập nhật sản phẩm
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }

        $product->name             = $request->name ?? $product->name;
        $product->slug             = Str::slug($request->name ?? $product->name) . '-' . $product->id;
        $product->description      = $request->description ?? $product->description;
        $product->category_id      = $request->category_id ?? $product->category_id;
        $product->category_item_id = $request->category_item_id ?? $product->category_item_id;
        $product->is_active        = $request->is_active ?? $product->is_active;

        // Cập nhật thumbnail
        if ($request->hasFile('thumbnail')) {
            // Xóa ảnh cũ nếu tồn tại
            if ($product->thumbnail && file_exists(public_path('images/' . $product->thumbnail))) {
                unlink(public_path('images/' . $product->thumbnail));
            }
            $file     = $request->file('thumbnail');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $fileName);
            $product->thumbnail = $fileName;
        }

        $product->save();

        // Cập nhật ảnh gallery mới (nếu có upload thêm)
        if ($request->hasFile('gallery')) {
            $existingCount = $product->images()->count();
            foreach ($request->file('gallery') as $index => $img) {
                $imgName = time() . '_' . $index . '_' . $img->getClientOriginalName();
                $img->move(public_path('images'), $imgName);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imgName,
                    'is_main'    => false,
                    'sort_order' => $existingCount + $index,
                ]);
            }
        }


        // Cập nhật biến thể: xóa cũ → thêm mới
        if ($request->has('variants')) {
            $variants = is_string($request->variants)
                ? json_decode($request->variants, true)
                : $request->variants;

            $product->variants()->delete();

            foreach ($variants as $v) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size_id'    => $v['size_id'] ?? null,
                    'price'      => $v['price'],
                    'sale_price' => $v['sale_price'] ?? null,
                    'stock'      => $v['stock'] ?? 0,
                    'sku'        => $v['sku'] ?? null,
                    'is_active'  => true,
                ]);
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Cập nhật sản phẩm thành công',
            'data'    => $product->load(['variants.size', 'images'])
        ]);
    }

    // DELETE /api/product/{id} — Xóa sản phẩm
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }

        // Xóa ảnh thumbnail
        if ($product->thumbnail && file_exists(public_path('images/' . $product->thumbnail))) {
            unlink(public_path('images/' . $product->thumbnail));
        }

        // Xóa ảnh gallery
        foreach ($product->images as $img) {
            if (file_exists(public_path('images/' . $img->image_path))) {
                unlink(public_path('images/' . $img->image_path));
            }
        }


        // Cascade tự xóa variants và images (nhờ onDelete cascade trong migration)
        $product->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Xóa sản phẩm thành công',
        ]);
    }

    // DELETE /api/product/image/{imageId} — Xóa 1 ảnh gallery riêng lẻ
    public function destroyImage($imageId)
    {
        $image = ProductImage::find($imageId);

        if (!$image) {
            return response()->json(['message' => 'Không tìm thấy ảnh'], 404);
        }

        if (file_exists(public_path('images/' . $image->image_path))) {
            unlink(public_path('images/' . $image->image_path));
        }


        $image->delete();

        return response()->json(['status' => 'success', 'message' => 'Xóa ảnh thành công']);
    }
}
