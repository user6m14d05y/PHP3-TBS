<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    protected $fillable = [
        'coupon_id',
        'user_id',
        'order_id',
        'discount_amount',
        'status',
        'reserved_at',
        'used_at',
        'released_at',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'reserved_at' => 'datetime',
        'used_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
