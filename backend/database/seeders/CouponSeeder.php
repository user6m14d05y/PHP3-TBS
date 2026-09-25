<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'name' => 'Giam 10% cho khach moi',
                'description' => 'Ap dung cho don hang tu 300000 VND.',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'max_discount_amount' => 100000,
                'min_order_amount' => 300000,
                'usage_limit' => 100,
                'per_user_limit' => 1,
                'conditions' => null,
            ],
            [
                'code' => 'FREESHIP30',
                'name' => 'Ho tro phi giao hang',
                'description' => 'Giam co dinh 30000 VND cho don tu 250000 VND.',
                'discount_type' => 'fixed_amount',
                'discount_value' => 30000,
                'max_discount_amount' => null,
                'min_order_amount' => 250000,
                'usage_limit' => 200,
                'per_user_limit' => 2,
                'conditions' => null,
            ],
            [
                'code' => 'BIGORDER15',
                'name' => 'Don lon giam 15%',
                'description' => 'Ap dung cho don hang tu 1000000 VND.',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'max_discount_amount' => 250000,
                'min_order_amount' => 1000000,
                'usage_limit' => 50,
                'per_user_limit' => 1,
                'conditions' => ['min_quantity' => 2],
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::query()->updateOrCreate(
                ['code' => $coupon['code']],
                [
                    ...$coupon,
                    'used_count' => 0,
                    'starts_at' => now()->subDay(),
                    'expires_at' => now()->addMonths(3),
                    'is_active' => true,
                ]
            );
        }
    }
}
