<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Seeder;

class UserAddressSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('email', 'test@example.com')->first();

        if (!$user) {
            return;
        }

        Shop::query()->updateOrCreate(
            ['name' => 'TBS Flower Quan 1'],
            [
                'phone' => '0909000001',
                'email' => 'shop.q1@example.com',
                'address_line' => '123 Nguyen Hue',
                'ward' => 'Ben Nghe',
                'district' => 'Quan 1',
                'city' => 'Ho Chi Minh',
                'latitude' => 10.7758439,
                'longitude' => 106.7017555,
                'delivery_radius_km' => 30,
                'is_active' => true,
            ]
        );

        Shop::query()->updateOrCreate(
            ['name' => 'TBS Flower Thu Duc'],
            [
                'phone' => '0909000002',
                'email' => 'shop.td@example.com',
                'address_line' => '25 Vo Van Ngan',
                'ward' => 'Linh Chieu',
                'district' => 'Thu Duc',
                'city' => 'Ho Chi Minh',
                'latitude' => 10.8494096,
                'longitude' => 106.7717987,
                'delivery_radius_km' => 30,
                'is_active' => true,
            ]
        );

        UserAddress::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'label' => 'Nha rieng',
            ],
            [
                'recipient_name' => 'Test User',
                'phone' => '0909123456',
                'email' => 'test@example.com',
                'address_line' => '72 Le Thanh Ton',
                'ward' => 'Ben Nghe',
                'district' => 'Quan 1',
                'city' => 'Ho Chi Minh',
                'postal_code' => '700000',
                'formatted_address' => '72 Le Thanh Ton, Ben Nghe, Quan 1, Ho Chi Minh',
                'latitude' => 10.7797838,
                'longitude' => 106.6990189,
                'place_id' => 'seed-hcm-q1-home',
                'is_default' => true,
            ]
        );

        UserAddress::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'label' => 'Van phong',
            ],
            [
                'recipient_name' => 'Test User',
                'phone' => '0909123456',
                'email' => 'test@example.com',
                'address_line' => '1 Cong Xa Paris',
                'ward' => 'Ben Nghe',
                'district' => 'Quan 1',
                'city' => 'Ho Chi Minh',
                'postal_code' => '700000',
                'formatted_address' => '1 Cong Xa Paris, Ben Nghe, Quan 1, Ho Chi Minh',
                'latitude' => 10.7797658,
                'longitude' => 106.6991097,
                'place_id' => 'seed-hcm-q1-office',
                'is_default' => false,
            ]
        );
    }
}
