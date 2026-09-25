<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('SALE####')),
            'name' => fake()->words(3, true),
            'discount_type' => fake()->randomElement(['percentage', 'fixed_amount']),
            'discount_value' => fake()->numberBetween(5, 20),
            'max_discount_amount' => 100000,
            'min_order_amount' => 200000,
            'usage_limit' => 100,
            'used_count' => 0,
            'per_user_limit' => 1,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
            'is_active' => true,
        ];
    }
}
