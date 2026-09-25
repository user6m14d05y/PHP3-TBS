<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ trước khi seed (tránh trùng lặp)
        Size::truncate();

        // Tạo danh sách kích thước mặc định
        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];

        foreach ($sizes as $sizeName) {
            Size::create(['name' => $sizeName]);
        }
    }
}
