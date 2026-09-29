<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    public const STATUSES = [
        'new'         => 'Nouveau',
        'in_progress' => 'En cours',
        'answered'    => 'Répondu',
        'closed'      => 'Clôturé',
    ];

    protected $fillable = [
        'user_id',
        'order_id',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'attachment',
        'status',
        'answered_at',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
    ];

    protected $appends = [
        'status_label',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function replies()
    {
        return $this->hasMany(ContactMessageReply::class)->latest();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Recherche : nom, email, téléphone, sujet ou référence de commande.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $like = '%' . addcslashes($term, '\\%_') . '%';

        return $query->where(function (Builder $q) use ($like) {
            $q->where('name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('phone', 'like', $like)
                ->orWhere('subject', 'like', $like)
                ->orWhereHas('order', fn (Builder $o) => $o->where('reference', 'like', $like));
        });
    }
}
