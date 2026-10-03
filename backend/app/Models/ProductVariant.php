<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $table = 'product_variants';

    protected $fillable = [
        'product_id',
        'size_id',
        'price',
        'sale_price',
        'stock',
        'sku',
        'is_active',
    ];

    /** Biến thể thuộc về sản phẩm nào */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /** Biến thể thuộc size nào */
    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }
}
