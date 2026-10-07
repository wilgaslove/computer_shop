<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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

    /**
     * Changements de statut autorisés (une commande livrée ou annulée est terminée).
     */
    public const TRANSITIONS = [
        'pending'   => ['confirmed', 'cancelled'],
        'confirmed' => ['shipped', 'cancelled'],
        'shipped'   => ['delivered'],
        'delivered' => [],
        'cancelled' => [],
    ];

    public const PAYMENT_METHODS = [
        'cash_on_delivery' => 'Paiement à la livraison',
        'mobile_money'     => 'Mobile Money',          // ancien mode, conservé pour l'historique
        'kkiapay'          => 'Paiement en ligne (KkiaPay)',
    ];

    /** Modes proposés au checkout. */
    public const CHECKOUT_METHODS = ['cash_on_delivery', 'kkiapay'];

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
        'kkiapay_transaction_id',
        'paid_at',
        'total',
        'shipping_name',
        'phone',
        'city',
        'address',
        'notes',
    ];

    protected $casts = [
        'total'   => 'decimal:2',
        'paid_at' => 'datetime',
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

    public function getStatusLabelAttribute(): ?string
    {
        return self::STATUSES[$this->status ?? ''] ?? $this->status;
    }

    public function getPaymentMethodLabelAttribute(): ?string
    {
        return self::PAYMENT_METHODS[$this->payment_method ?? ''] ?? $this->payment_method;
    }

    public function getPaymentStatusLabelAttribute(): ?string
    {
        return self::PAYMENT_STATUSES[$this->payment_status ?? ''] ?? $this->payment_status;
    }

    /**
     * Statuts vers lesquels cette commande peut évoluer.
     */
    public function allowedTransitions(): array
    {
        return self::TRANSITIONS[$this->status] ?? [];
    }

    public function canBeCancelled(): bool
    {
        return in_array('cancelled', $this->allowedTransitions(), true);
    }

    /**
     * Annule la commande et remet les quantités en stock, en une seule transaction.
     * Retourne false si la commande n'est plus annulable (ex : déjà annulée par un double clic).
     */
    public function cancelAndRestock(): bool
    {
        return DB::transaction(function () {
            // Verrou : deux annulations simultanées ne peuvent pas remettre le stock deux fois.
            $fresh = static::whereKey($this->id)->lockForUpdate()->first();

            if (! $fresh->canBeCancelled()) {
                return false;
            }

            foreach ($fresh->items as $item) {
                if ($item->product_id) {
                    $item->product()->increment('stock', $item->quantity);
                }
            }

            $fresh->update(['status' => 'cancelled']);
            $this->refresh();

            return true;
        });
    }

    /**
     * Enregistre un paiement en ligne reçu. Idempotent : sans effet si la commande est déjà payée.
     * Une commande « en attente » passe automatiquement en « confirmée ».
     */
    public function markPaidOnline(string $transactionId): bool
    {
        return DB::transaction(function () use ($transactionId) {
            $fresh = static::whereKey($this->id)->lockForUpdate()->first();

            if ($fresh->payment_status === 'paid') {
                $this->refresh();

                return false;
            }

            $updates = [
                'payment_status'         => 'paid',
                'kkiapay_transaction_id' => $transactionId,
                'paid_at'                => now(),
            ];

            if ($fresh->status === 'pending') {
                $updates['status'] = 'confirmed';
            }

            $fresh->update($updates);
            $this->refresh();

            return true;
        });
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
