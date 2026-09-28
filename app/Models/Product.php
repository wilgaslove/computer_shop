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

    protected $appends = [
        'cover_image',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
    }

    /**
     * Image principale du produit : le champ "image" (aperçu/couverture)
     * s'il existe, sinon la première image de la galerie.
     */
    public function getCoverImageAttribute()
    {
        if ($this->image) {
            return $this->image;
        }

        $first = $this->relationLoaded('images')
            ? $this->images->first()
            : $this->images()->first();

        return $first?->path;
    }
}
