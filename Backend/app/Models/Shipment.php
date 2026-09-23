<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'provider',
        'tracking_code',
        'quoted_fee',
        'distance_km',
        'status',
        'quote_payload',
        'provider_payload',
    ];

    protected $casts = [
        'quoted_fee' => 'decimal:2',
        'distance_km' => 'decimal:2',
        'quote_payload' => 'array',
        'provider_payload' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
