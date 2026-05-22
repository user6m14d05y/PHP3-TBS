<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $limit = max(1, min($request->integer('limit', 12), 50));
        $includeInactive = $request->boolean('include_inactive');

        $products = Product::query()
            ->select(
                'id',
                'category_id',
                'category_item_id',
                'name',
                'slug',
                'description',
                'seo_title',
                'meta_description',
                'focus_keyword',
                'thumbnail',
                'image_alt',
                'is_active',
                'created_at',
                'updated_at'
            )
            ->with([
                'category:id,name,slug,img,seo_title,meta_description,seo_content',
                'categoryItem:id,category_id,name,slug,seo_title,meta_description,seo_content',
                'variants:id,product_id,size_id,price,sale_price,stock,sku,is_active',
                'variants.size:id,name',
                'images:id,product_id,image_path,is_main,sort_order',
            ])
            ->when(!$includeInactive, function ($query) {
                $query->where('is_active', true);
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->integer('category_id'));
            })
            ->when($request->filled('category_item_id'), function ($query) use ($request) {
                $query->where('category_item_id', $request->integer('category_item_id'));
            })
            ->latest('id')
            ->paginate($limit);

        return response()->json([
            'status' => 'success',
            'data' => $products->items(),
            'total' => $products->total(),
            'per_page' => $products->perPage(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
        ]);
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:1', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $query = Str::lower(trim($validated['q']));
        $limit = (int) ($validated['limit'] ?? 6);
        $cacheKey = 'product_search:' . md5($query) . ':' . $limit;

        $products = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($query, $limit) {
            return Product::query()
                ->select('id', 'category_id', 'category_item_id', 'name', 'slug', 'description', 'thumbnail', 'image_alt', 'is_active', 'created_at')
                ->with([
                    'category:id,name',
                    'categoryItem:id,category_id,name',
                    'variants:id,product_id,price,sale_price,is_active',
                ])
                ->where('is_active', true)
                ->where(function ($builder) use ($query) {
                    $builder->whereRaw('LOWER(name) LIKE ?', ["%{$query}%"])
                        ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$query}%"])
                        ->orWhereRaw('LOWER(description) LIKE ?', ["%{$query}%"])
                        ->orWhereHas('category', function ($categoryQuery) use ($query) {
                            $categoryQuery->whereRaw('LOWER(name) LIKE ?', ["%{$query}%"]);
                        })
                        ->orWhereHas('categoryItem', function ($categoryItemQuery) use ($query) {
                            $categoryItemQuery->whereRaw('LOWER(name) LIKE ?', ["%{$query}%"]);
                        });
                })
                ->latest('id')
                ->limit($limit)
                ->get()
                ->map(function (Product $product) {
                    $prices = $product->variants
                        ->map(fn ($variant) => (float) ($variant->sale_price ?: $variant->price))
                        ->filter(fn ($price) => $price > 0);

                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'thumbnail' => $product->thumbnail,
                        'image_alt' => $product->image_alt,
                        'category_name' => $product->categoryItem?->name ?: $product->category?->name,
                        'price' => $prices->min(),
                    ];
                })
                ->values();
        });

        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
    }

    public function show($slug)
    {
        $product = Product::with(['category', 'categoryItem', 'variants.size', 'images'])
            ->where('slug', $slug)
            ->when(is_numeric($slug), function ($query) use ($slug) {
                $query->orWhere('id', $slug);
            })
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $product]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        $product = new Product();
        $product->name = $validated['name'];
        $product->slug = $this->generateUniqueSlug($validated['slug'] ?? $validated['name']);
        $product->description = $validated['description'] ?? null;
        $product->seo_title = $validated['seo_title'] ?? null;
        $product->meta_description = $validated['meta_description'] ?? null;
        $product->focus_keyword = $validated['focus_keyword'] ?? null;
        $product->image_alt = $validated['image_alt'] ?? $validated['name'];
        $product->category_id = $validated['category_id'] ?? null;
        $product->category_item_id = $validated['category_item_id'] ?? null;
        $product->is_active = (bool) ($validated['is_active'] ?? true);

        if ($request->hasFile('thumbnail')) {
            $fileName = $this->buildImageFileName($request->file('thumbnail'), $product->slug);
            $request->file('thumbnail')->move(public_path('images'), $fileName);
            $product->thumbnail = $fileName;
        }

        $product->save();

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $index => $img) {
                $imgName = $this->buildImageFileName($img, $product->slug . '-gallery-' . ($index + 1));
                $img->move(public_path('images'), $imgName);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imgName,
                    'is_main' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        foreach ($validated['variants'] as $variant) {
            ProductVariant::create([
                'product_id' => $product->id,
                'size_id' => $variant['size_id'] ?? null,
                'price' => $variant['price'],
                'sale_price' => $variant['sale_price'] ?? null,
                'stock' => $variant['stock'] ?? 0,
                'sku' => $variant['sku'] ?? null,
                'is_active' => true,
            ]);
        }

        Cache::flush();

        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully',
            'data' => $product->load(['category', 'categoryItem', 'variants.size', 'images']),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validated = $this->validateProduct($request, $product->id);
        $originalName = $product->name;

        $product->name = $validated['name'];

        if (!empty($validated['slug'])) {
            $product->slug = $this->generateUniqueSlug($validated['slug'], $product->id);
        } elseif ($validated['name'] !== $originalName) {
            $product->slug = $this->generateUniqueSlug($validated['name'], $product->id);
        }

        $product->description = $validated['description'] ?? null;
        $product->seo_title = $validated['seo_title'] ?? null;
        $product->meta_description = $validated['meta_description'] ?? null;
        $product->focus_keyword = $validated['focus_keyword'] ?? null;
        $product->image_alt = $validated['image_alt'] ?? $product->name;
        $product->category_id = $validated['category_id'] ?? null;
        $product->category_item_id = $validated['category_item_id'] ?? null;
        $product->is_active = array_key_exists('is_active', $validated)
            ? (bool) $validated['is_active']
            : $product->is_active;

        if ($request->hasFile('thumbnail')) {
            if ($product->thumbnail && file_exists(public_path('images/' . $product->thumbnail))) {
                unlink(public_path('images/' . $product->thumbnail));
            }

            $fileName = $this->buildImageFileName($request->file('thumbnail'), $product->slug);
            $request->file('thumbnail')->move(public_path('images'), $fileName);
            $product->thumbnail = $fileName;
        }

        $product->save();

        if ($request->hasFile('gallery')) {
            $existingCount = $product->images()->count();
            foreach ($request->file('gallery') as $index => $img) {
                $imgName = $this->buildImageFileName($img, $product->slug . '-gallery-' . ($existingCount + $index + 1));
                $img->move(public_path('images'), $imgName);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imgName,
                    'is_main' => false,
                    'sort_order' => $existingCount + $index,
                ]);
            }
        }

        $product->variants()->delete();

        foreach ($validated['variants'] as $variant) {
            ProductVariant::create([
                'product_id' => $product->id,
                'size_id' => $variant['size_id'] ?? null,
                'price' => $variant['price'],
                'sale_price' => $variant['sale_price'] ?? null,
                'stock' => $variant['stock'] ?? 0,
                'sku' => $variant['sku'] ?? null,
                'is_active' => true,
            ]);
        }

        Cache::flush();

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully',
            'data' => $product->load(['category', 'categoryItem', 'variants.size', 'images']),
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        if ($product->thumbnail && file_exists(public_path('images/' . $product->thumbnail))) {
            unlink(public_path('images/' . $product->thumbnail));
        }

        foreach ($product->images as $img) {
            if (file_exists(public_path('images/' . $img->image_path))) {
                unlink(public_path('images/' . $img->image_path));
            }
        }

        $product->delete();
        Cache::flush();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully',
        ]);
    }

    public function destroyImage($imageId)
    {
        $image = ProductImage::find($imageId);

        if (!$image) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        if (file_exists(public_path('images/' . $image->image_path))) {
            unlink(public_path('images/' . $image->image_path));
        }

        $image->delete();
        Cache::flush();

        return response()->json(['status' => 'success', 'message' => 'Image deleted successfully']);
    }

    private function validateProduct(Request $request, ?int $productId = null): array
    {
        $variants = $request->input('variants', []);

        foreach (['category_id', 'category_item_id'] as $field) {
            if ($request->input($field) === '') {
                $request->merge([$field => null]);
            }
        }

        if (is_string($variants)) {
            $decodedVariants = json_decode($variants, true);
            $request->merge(['variants' => is_array($decodedVariants) ? $decodedVariants : []]);
        }

        $variants = collect($request->input('variants', []))
            ->map(function ($variant) {
                if (!is_array($variant)) {
                    return $variant;
                }

                foreach (['size_id', 'sale_price', 'discount_percent', 'sku'] as $field) {
                    if (($variant[$field] ?? null) === '') {
                        $variant[$field] = null;
                    }
                }

                if (empty($variant['sale_price']) && !empty($variant['discount_percent']) && !empty($variant['price'])) {
                    $discountPercent = min(max((float) $variant['discount_percent'], 0), 99);
                    $variant['sale_price'] = round((float) $variant['price'] * (100 - $discountPercent) / 100);
                }

                if (($variant['stock'] ?? null) === '') {
                    $variant['stock'] = 0;
                }

                return $variant;
            })
            ->all();

        $request->merge(['variants' => $variants]);

        return Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:170'],
            'focus_keyword' => ['nullable', 'string', 'max:120'],
            'image_alt' => ['nullable', 'string', 'max:160'],
            'category_id' => ['nullable', 'integer', 'exists:category,id'],
            'category_item_id' => ['nullable', 'integer', 'exists:category_item,id'],
            'is_active' => ['nullable', 'boolean'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.size_id' => ['nullable', 'integer', 'exists:size,id'],
            'variants.*.price' => ['required', 'numeric', 'min:1'],
            'variants.*.sale_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:99'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.sku' => ['nullable', 'string', 'max:100'],
        ])->validate();
    }

    private function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        $base = $base !== '' ? $base : 'product';
        $base = substr($base, 0, 110);

        $slug = $base;
        $counter = 2;

        while (
            Product::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $suffix = '-' . $counter++;
            $slug = substr($base, 0, 120 - strlen($suffix)) . $suffix;
        }

        return $slug;
    }

    private function buildImageFileName($file, string $name): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $base = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $base = $base !== '' ? $base : Str::slug($name);

        return time() . '_' . Str::random(6) . '_' . substr($base, 0, 80) . '.' . $extension;
    }
}
