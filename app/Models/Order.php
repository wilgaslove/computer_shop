<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    public const STATUSES = [
        'pending'   => 'En attente',
        'confirmed' => 'Confirmée',
        'shipped'   => 'Expédiée',
        'delivered' => 'Livrée',
        'cancelled' => 'Annulée',
    ];

    public const PAYMENT_METHODS = [
        'cash_on_delivery' => 'Paiement à la livraison',
        'mobile_money'     => 'Mobile Money',
    ];

    public const PAYMENT_STATUSES = [
        'unpaid' => 'Non payée',
        'paid'   => 'Payée',
    ];

    protected $fillable = [
        'reference',
        'user_id',
        'status',
        'payment_method',
        'payment_status',
        'total',
        'shipping_name',
        'phone',
        'city',
        'address',
        'notes',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    protected $appends = [
        'status_label',
        'payment_method_label',
        'payment_status_label',
    ];

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status] ?? $this->payment_status;
    }

    /**
     * Référence lisible et unique, ex : CS-260928-K4X9Q2
     */
    public static function generateReference(): string
    {
        do {
            $reference = 'CS-' . now()->format('ymd') . '-' . Str::upper(Str::random(6));
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }
}
