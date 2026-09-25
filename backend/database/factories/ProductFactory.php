<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Hoa Hồng Pastel',
            'Bó Hoa Tình Yêu',
            'Hoa Khai Trương Rực Rỡ',
            'Lẵng Hoa Cát Tường',
            'Hoa Sinh Nhật Dịu Dàng',
            'Bó Hoa Baby Trắng',
            'Hoa Hướng Dương Nắng Mai',
            'Giỏ Hoa Yêu Thương',
            'Hoa Đồng Tiền May Mắn',
            'Hoa Cẩm Tú Cầu Xanh',
            'Bó Hoa Tulip Hồng',
            'Hoa Ly Trắng Thanh Khiết',
            'Kệ Hoa Chúc Mừng',
            'Hoa Lan Hồ Điệp',
            'Hoa Tốt Nghiệp',
            'Bó Hoa Lavender',
            'Hoa Cúc Tana',
            'Giỏ Hoa An Nhiên',
            'Hoa Hồng Đỏ Sang Trọng',
            'Bó Hoa Mùa Xuân',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'description' => $this->faker->paragraph(3),
            'thumbnail' => $this->faker->randomElement([
                'hoa-hong.jpg',
                'hoa-khai-truong.jpg',
                'hoa-sinh-nhat.jpg',
                'hoa-tot-nghiep.jpg',
                'hoa-tulip.jpg',
                'hoa-lan.jpg',
            ]),
            'is_active' => true,
        ];
    }
}
