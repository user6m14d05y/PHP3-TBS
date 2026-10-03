<?php

namespace Database\Factories;

use App\Models\Size;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Size>
 */
class SizeFactory extends Factory
{
    protected $model = Size::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Danh sách các kích thước quần áo phổ biến
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL', '38', '39', '40', '41', '42', '43'];

        return [
            'name' => $this->faker->unique()->randomElement($sizes),
        ];
    }
}
