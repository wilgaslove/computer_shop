<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'promo_price',
        'stock',
        'category_id',
        'description',
        'active',
        'image',
    ];

    protected $casts = [
        'price'       => 'decimal:2',
        'promo_price' => 'decimal:2',
    ];

    protected $appends = [
        'cover_image',
        'current_price',
        'is_on_promotion',
        'discount_percent',
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

    /**
     * Un produit est en promotion si son prix promo est renseigné
     * et strictement inférieur au prix normal.
     */
    public function getIsOnPromotionAttribute(): bool
    {
        return $this->promo_price !== null
            && (float) $this->promo_price > 0
            && (float) $this->promo_price < (float) $this->price;
    }

    /**
     * Prix réellement facturé : le prix promo si le produit est en promotion,
     * sinon le prix normal. C'est CE prix qu'il faut utiliser pour le panier et les commandes.
     */
    public function getCurrentPriceAttribute(): float
    {
        return $this->is_on_promotion
            ? (float) $this->promo_price
            : (float) $this->price;
    }

    /**
     * Pourcentage de réduction arrondi (ex : 25 pour -25 %).
     */
    public function getDiscountPercentAttribute(): int
    {
        if (! $this->is_on_promotion) {
            return 0;
        }

        return (int) round((1 - (float) $this->promo_price / (float) $this->price) * 100);
    }

    /**
     * Produits actifs actuellement en promotion.
     */
    public function scopeOnPromotion(Builder $query): Builder
    {
        return $query->where('active', true)
            ->whereNotNull('promo_price')
            ->where('promo_price', '>', 0)
            ->whereColumn('promo_price', '<', 'price');
    }
}
