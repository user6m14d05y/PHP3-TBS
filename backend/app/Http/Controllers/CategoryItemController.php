<?php

namespace App\Http\Controllers;

use App\Models\CategoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('category_id')) {
            $items = CategoryItem::select('id', 'category_id', 'name', 'slug', 'seo_title', 'meta_description', 'seo_content')
                ->where('category_id', $request->category_id)
                ->orderBy('id')
                ->get();
        } else {
            $items = CategoryItem::select('id', 'category_id', 'name', 'slug', 'seo_title', 'meta_description', 'seo_content')
                ->with('category:id,name,slug,img')
                ->orderBy('category_id')
                ->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:category,id'],
            'name' => ['required', 'string', 'max:50'],
            'slug' => ['nullable', 'string', 'max:100'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:170'],
            'seo_content' => ['nullable', 'string'],
        ]);

        $item = CategoryItem::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug($validated['slug'] ?? $validated['name']),
            'seo_title' => $validated['seo_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'seo_content' => $validated['seo_content'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category item created successfully',
            'data' => $item,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $item = CategoryItem::find($id);

        if (!$item) {
            return response()->json(['message' => 'Category item not found'], 404);
        }

        $validated = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:category,id'],
            'name' => ['nullable', 'string', 'max:50'],
            'slug' => ['nullable', 'string', 'max:100'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:170'],
            'seo_content' => ['nullable', 'string'],
        ]);

        $originalName = $item->name;
        $item->category_id = $validated['category_id'] ?? $item->category_id;
        $item->name = $validated['name'] ?? $item->name;

        if (!empty($validated['slug'])) {
            $item->slug = $this->generateUniqueSlug($validated['slug'], $item->id);
        } elseif ($item->name !== $originalName) {
            $item->slug = $this->generateUniqueSlug($item->name, $item->id);
        }

        foreach (['seo_title', 'meta_description', 'seo_content'] as $field) {
            if (array_key_exists($field, $validated)) {
                $item->{$field} = $validated[$field];
            }
        }
        $item->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Category item updated successfully',
            'data' => $item,
        ]);
    }

    public function destroy($id)
    {
        $item = CategoryItem::find($id);

        if (!$item) {
            return response()->json(['message' => 'Category item not found'], 404);
        }

        $item->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Category item deleted successfully',
        ]);
    }

    private function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        $base = $base !== '' ? $base : 'category-item';
        $base = substr($base, 0, 90);
        $slug = $base;
        $counter = 2;

        while (
            CategoryItem::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $suffix = '-' . $counter++;
            $slug = substr($base, 0, 100 - strlen($suffix)) . $suffix;
        }

        return $slug;
    }
}
