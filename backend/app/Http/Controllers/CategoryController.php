<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::select('id', 'name', 'slug', 'img', 'seo_title', 'meta_description', 'seo_content')
            ->orderBy('id')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $category,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'slug' => ['nullable', 'string', 'max:90'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:170'],
            'seo_content' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'img' => ['nullable', 'string', 'max:255'],
        ]);

        $category = new Category();
        $category->name = $validated['name'];
        $category->slug = $this->generateUniqueSlug($validated['slug'] ?? $validated['name']);
        foreach (['seo_title', 'meta_description', 'seo_content'] as $field) {
            if (array_key_exists($field, $validated)) {
                $category->{$field} = $validated[$field];
            }
        }

        if ($request->hasFile('image')) {
            $imageName = $this->buildImageFileName($request->file('image'), $category->slug);
            $request->file('image')->move(public_path('images'), $imageName);
            $category->img = $imageName;
        } else {
            $category->img = $validated['img'] ?? null;
        }

        $category->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:50'],
            'slug' => ['nullable', 'string', 'max:90'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:170'],
            'seo_content' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'img' => ['nullable', 'string', 'max:255'],
        ]);

        $originalName = $category->name;
        $category->name = $validated['name'] ?? $category->name;

        if (!empty($validated['slug'])) {
            $category->slug = $this->generateUniqueSlug($validated['slug'], $category->id);
        } elseif ($category->name !== $originalName) {
            $category->slug = $this->generateUniqueSlug($category->name, $category->id);
        }

        foreach (['seo_title', 'meta_description', 'seo_content'] as $field) {
            if (array_key_exists($field, $validated)) {
                $category->{$field} = $validated[$field];
            }
        }

        if ($request->hasFile('image')) {
            $imageName = $this->buildImageFileName($request->file('image'), $category->slug);
            $request->file('image')->move(public_path('images'), $imageName);

            if ($category->img && file_exists(public_path('images/' . $category->img))) {
                unlink(public_path('images/' . $category->img));
            }

            $category->img = $imageName;
        } elseif (array_key_exists('img', $validated)) {
            $category->img = $validated['img'];
        }

        $category->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully',
            'data' => $category,
        ]);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        if ($category->img && file_exists(public_path('images/' . $category->img))) {
            unlink(public_path('images/' . $category->img));
        }

        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Category deleted successfully',
        ]);
    }

    private function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        $base = $base !== '' ? $base : 'category';
        $base = substr($base, 0, 80);
        $slug = $base;
        $counter = 2;

        while (
            Category::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $suffix = '-' . $counter++;
            $slug = substr($base, 0, 90 - strlen($suffix)) . $suffix;
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
