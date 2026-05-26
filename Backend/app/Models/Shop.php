<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address_line',
        'ward',
        'district',
        'city',
        'latitude',
        'longitude',
        'delivery_radius_km',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'delivery_radius_km' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
