<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $imagePool = $this->imagePool();

        $category = Category::query()->updateOrCreate(
            ['name' => 'Hoa tuoi'],
            [
                'slug' => 'hoa-tuoi',
                'img' => $imagePool->first(),
                'seo_title' => 'Hoa tuoi TBS',
                'meta_description' => 'Danh muc hoa tuoi giao nhanh trong khu vuc ho tro.',
                'seo_content' => 'Bo suu tap hoa tuoi cho sinh nhat, khai truong, tot nghiep va cac dip dac biet.',
            ]
        );

        $categoryItems = collect([
            'Hoa bo',
            'Hoa khai truong',
            'Hoa sinh nhat',
            'Hoa tot nghiep',
            'Hoa lan',
        ])->mapWithKeys(function (string $name) use ($category) {
            $item = CategoryItem::query()->updateOrCreate(
                [
                    'category_id' => $category->id,
                    'name' => $name,
                ],
                [
                    'slug' => Str::slug($name),
                    'seo_title' => $name,
                    'meta_description' => 'Cac mau ' . Str::lower($name) . ' dep, co san va co the giao trong ngay.',
                    'seo_content' => 'Danh muc ' . Str::lower($name) . ' duoc chon loc cho nhu cau dat hoa online.',
                ]
            );

            return [$name => $item];
        });

        $sizes = collect(['S', 'M', 'L', 'XL'])->map(function (string $name) {
            return Size::query()->updateOrCreate(['name' => $name]);
        })->values();

        foreach ($this->products() as $index => $data) {
            $slug = Str::slug($data['name']);
            $images = $this->pickImages($imagePool, $index * 4, 4);
            $categoryItem = $categoryItems[$data['category_item']] ?? $categoryItems->first();

            $product = Product::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $category->id,
                    'category_item_id' => $categoryItem->id,
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'seo_title' => $data['name'],
                    'meta_description' => 'Dat ' . Str::lower($data['name']) . ' tai TBS Flower, giao nhanh trong ban kinh ho tro.',
                    'focus_keyword' => Str::lower($data['name']),
                    'thumbnail' => $images[0] ?? null,
                    'image_alt' => $data['name'],
                    'is_active' => true,
                ]
            );

            $product->images()->delete();
            foreach ($images as $sortOrder => $image) {
                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'image_path' => $image,
                    'is_main' => $sortOrder === 0,
                    'sort_order' => $sortOrder,
                ]);
            }

            foreach ($sizes as $variantIndex => $size) {
                $price = $data['base_price'] + ($variantIndex * 90000);
                $salePrice = $variantIndex === 0 ? null : $price - 30000;

                ProductVariant::query()->updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'size_id' => $size->id,
                    ],
                    [
                        'price' => $price,
                        'sale_price' => $salePrice,
                        'stock' => 15 + (($index + $variantIndex) % 35),
                        'sku' => 'TBS-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT) . '-' . Str::upper($size->name),
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    /**
     * @return array<int, array{name: string, category_item: string, base_price: int, description: string}>
     */
    private function products(): array
    {
        return [
            ['name' => 'Bo hoa hong do lua tinh', 'category_item' => 'Hoa bo', 'base_price' => 350000, 'description' => 'Bo hoa hong do tone am, phu hop tang nguoi yeu, ky niem va cac dip dac biet.'],
            ['name' => 'Bo hoa pastel nhe nhang', 'category_item' => 'Hoa bo', 'base_price' => 320000, 'description' => 'Mau hoa pastel thanh lich, de tang ban be, dong nghiep hoac nguoi than.'],
            ['name' => 'Bo hoa baby trang tinh khoi', 'category_item' => 'Hoa bo', 'base_price' => 290000, 'description' => 'Bo hoa baby trang gon dep, mau sac nhe va de phoi trong nhieu dip.'],
            ['name' => 'Bo hoa tulip hong Ha Lan', 'category_item' => 'Hoa bo', 'base_price' => 520000, 'description' => 'Tulip hong sang trong, thich hop cho nhung don hang can su tinh te.'],
            ['name' => 'Gio hoa yeu thuong', 'category_item' => 'Hoa sinh nhat', 'base_price' => 450000, 'description' => 'Gio hoa nhieu mau, bo cuc day dan cho sinh nhat va loi chuc may man.'],
            ['name' => 'Hoa sinh nhat diu dang', 'category_item' => 'Hoa sinh nhat', 'base_price' => 380000, 'description' => 'Mau hoa sinh nhat de tang, co the kem thiep chuc mung theo yeu cau.'],
            ['name' => 'Hoa huong duong nang mai', 'category_item' => 'Hoa sinh nhat', 'base_price' => 310000, 'description' => 'Huong duong tuoi sang, gui thong diep lac quan va nang luong tich cuc.'],
            ['name' => 'Lang hoa khai truong ruc ro', 'category_item' => 'Hoa khai truong', 'base_price' => 890000, 'description' => 'Lang hoa khai truong mau sac noi bat, phu hop gui tang cua hang va cong ty.'],
            ['name' => 'Ke hoa chuc mung thanh cong', 'category_item' => 'Hoa khai truong', 'base_price' => 1250000, 'description' => 'Ke hoa chuc mung quy mo lon, thiet ke trang trong cho su kien quan trong.'],
            ['name' => 'Lang hoa cat tuong', 'category_item' => 'Hoa khai truong', 'base_price' => 760000, 'description' => 'Lang hoa cat tuong voi thong diep phat tai va thuan loi trong kinh doanh.'],
            ['name' => 'Hoa tot nghiep niem vui', 'category_item' => 'Hoa tot nghiep', 'base_price' => 330000, 'description' => 'Bo hoa tot nghiep tre trung, de cam tay trong ngay le tot nghiep.'],
            ['name' => 'Bo hoa tot nghiep gau nho', 'category_item' => 'Hoa tot nghiep', 'base_price' => 410000, 'description' => 'Hoa tot nghiep ket hop phu kien nho, phu hop tang ban be va nguoi than.'],
            ['name' => 'Hoa lan ho diep trang', 'category_item' => 'Hoa lan', 'base_price' => 980000, 'description' => 'Chau lan ho diep trang thanh lich cho van phong, nha rieng va qua tang cao cap.'],
            ['name' => 'Hoa lan ho diep vang', 'category_item' => 'Hoa lan', 'base_price' => 1050000, 'description' => 'Lan ho diep vang mang y nghia may man, thich hop chuc mung khai truong.'],
            ['name' => 'Hoa hong Ecuador cao cap', 'category_item' => 'Hoa bo', 'base_price' => 690000, 'description' => 'Hoa hong Ecuador bong lon, phu hop don qua cao cap can hinh anh an tuong.'],
            ['name' => 'Bo hoa lavender mong mo', 'category_item' => 'Hoa bo', 'base_price' => 430000, 'description' => 'Bo hoa tone tim nhe, tao cam giac lang man va khac biet.'],
            ['name' => 'Hoa cuc tana trong veo', 'category_item' => 'Hoa bo', 'base_price' => 260000, 'description' => 'Cuc tana nho xinh, phu hop phong cach toi gian va tu nhien.'],
            ['name' => 'Gio hoa an nhien', 'category_item' => 'Hoa sinh nhat', 'base_price' => 470000, 'description' => 'Gio hoa tone trang xanh, gui loi chuc binh an va nhe nhang.'],
            ['name' => 'Bo hoa mua xuan', 'category_item' => 'Hoa bo', 'base_price' => 390000, 'description' => 'Bo hoa phoi mau tuoi sang, phu hop cho cac dip chuc mung trong nam.'],
            ['name' => 'Hoa cam tu cau xanh', 'category_item' => 'Hoa bo', 'base_price' => 560000, 'description' => 'Cam tu cau xanh noi bat, thich hop lam qua tang thanh lich va doc dao.'],
        ];
    }

    private function imagePool(): Collection
    {
        $extensions = ['jpg', 'jpeg', 'png', 'webp', 'JPG', 'JPEG', 'PNG', 'WEBP'];

        $files = collect($extensions)
            ->flatMap(fn (string $extension) => glob(public_path('images/*.' . $extension)) ?: [])
            ->map(fn (string $path) => basename($path))
            ->unique()
            ->values();

        $flowerImages = $files->filter(function (string $name) {
            $lowerName = Str::lower($name);

            return str_contains($lowerName, 'hoa')
                || str_contains($lowerName, 'flower')
                || str_contains($lowerName, 'bo-');
        })->values();

        return $flowerImages->count() >= 4 ? $flowerImages : $files;
    }

    /**
     * @return array<int, string>
     */
    private function pickImages(Collection $imagePool, int $start, int $count): array
    {
        if ($imagePool->isEmpty()) {
            return [];
        }

        return collect(range(0, $count - 1))
            ->map(fn (int $offset) => $imagePool[($start + $offset) % $imagePool->count()])
            ->unique()
            ->values()
            ->all();
    }
}
