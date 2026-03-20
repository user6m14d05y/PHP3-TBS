<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryItem extends Model
{
    protected $table = 'category_item';

    protected $fillable = [
        'category_id',
        'name',
    ];

    /**
     * Quan hệ: Danh mục con thuộc về 1 Danh mục cha.
     * Dùng: $item->category->name
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
