<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'img',
    ];

    /**
     * Quan hệ: 1 Danh mục cha có nhiều Danh mục con.
     * Dùng: $category->items (trả về danh sách CategoryItem)
     */
    public function items()
    {
        return $this->hasMany(CategoryItem::class, 'category_id');
    }
}
