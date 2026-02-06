<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'stock',
        'category_id',
        'description',
        'active',
        'image',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
