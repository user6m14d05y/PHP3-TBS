<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $hidden = [
        'id',
    ];

    protected $fillable = [
        'order_code',
        'user_id',
        'shop_id',
        'user_address_id',
        'coupon_id',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'shipping_address',
        'shipping_latitude',
        'shipping_longitude',
        'delivery_distance_km',
        'subtotal_amount',
        'discount_amount',
        'shipping_fee',
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        'note',
    ];

    protected $casts = [
        'shipping_latitude' => 'decimal:7',
        'shipping_longitude' => 'decimal:7',
        'delivery_distance_km' => 'decimal:2',
        'subtotal_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function getRouteKeyName(): string
    {
        return 'order_code';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
