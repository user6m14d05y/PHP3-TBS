<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    /** @use HasFactory<\Database\Factories\UserAddressFactory> */
    use HasFactory;

    protected $table = 'user_addresses';

    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'email',
        'address_line',
        'ward',
        'district',
        'city',
        'postal_code',
        'formatted_address',
        'latitude',
        'longitude',
        'place_id',
        'is_default',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
