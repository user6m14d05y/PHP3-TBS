<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $table = 'size';
    // xoá các field created_at và updated_at
    public $timestamps = false;
    
    protected $fillable = [
        'name',
    ];
}
