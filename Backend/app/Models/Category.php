<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category';
    // xoá các field created_at và updated_at
    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'img',
    ];
}
