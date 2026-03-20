<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'category_item_id',
        'name',
        'slug',
        'description',
        'thumbnail',
        'is_active',
    ];

    /** 1 sản phẩm thuộc 1 danh mục cha */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /** 1 sản phẩm thuộc 1 danh mục con */
    public function categoryItem()
    {
        return $this->belongsTo(CategoryItem::class, 'category_item_id');
    }

    /** 1 sản phẩm có nhiều biến thể (size + giá) */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id')->orderBy('price', 'asc');
    }

    /** 1 sản phẩm có nhiều ảnh gallery */
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id')->orderBy('sort_order', 'asc');
    }
}
