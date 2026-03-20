<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = 'product_images';

    protected $fillable = [
        'product_id',
        'image_path',
        'is_main',
        'sort_order',
    ];

    /** Ảnh thuộc về sản phẩm nào */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
