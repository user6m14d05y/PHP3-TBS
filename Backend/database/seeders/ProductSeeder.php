<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = Category::query()->firstOrCreate(
            ['name' => 'Hoa tươi'],
            ['img' => 'category-default.jpg']
        );

        $categoryItems = collect(['Hoa bó', 'Hoa khai trương', 'Hoa sinh nhật', 'Hoa tốt nghiệp'])
            ->map(function (string $name) use ($category) {
                return CategoryItem::query()->firstOrCreate([
                    'category_id' => $category->id,
                    'name' => $name,
                ]);
            });

        $sizes = collect(['S', 'M', 'L'])->map(function (string $name) {
            return Size::query()->firstOrCreate(['name' => $name]);
        });

        Product::factory(20)->create()->each(function (Product $product) use ($category, $categoryItems, $sizes) {
            $categoryItem = $categoryItems->random();
            $size = $sizes->random();
            $price = fake()->numberBetween(250, 2500) * 1000;
            $salePrice = fake()->boolean(35) ? $price - fake()->numberBetween(20, 200) * 1000 : null;

            $product->update([
                'category_id' => $category->id,
                'category_item_id' => $categoryItem->id,
            ]);

            ProductVariant::query()->create([
                'product_id' => $product->id,
                'size_id' => $size->id,
                'price' => $price,
                'sale_price' => $salePrice,
                'stock' => fake()->numberBetween(5, 80),
                'sku' => 'SP-' . $product->id . '-' . Str::upper(Str::random(6)),
                'is_active' => true,
            ]);
        });
    }
}
