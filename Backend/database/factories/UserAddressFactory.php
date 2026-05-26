<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserAddress>
 */
class UserAddressFactory extends Factory
{
    protected $model = UserAddress::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => 'Nha rieng',
            'recipient_name' => fake()->name(),
            'phone' => fake()->numerify('09########'),
            'email' => fake()->safeEmail(),
            'address_line' => fake()->streetAddress(),
            'ward' => fake()->citySuffix(),
            'district' => fake()->city(),
            'city' => fake()->city(),
            'formatted_address' => fake()->address(),
            'latitude' => fake()->latitude(10.0, 11.0),
            'longitude' => fake()->longitude(106.0, 107.0),
            'is_default' => false,
        ];
    }
}
