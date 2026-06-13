<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'artwork_id',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'stripe_customer_email',
        'customer_name',
        'customer_phone',
        'amount_total',
        'currency',
        'payment_status',
        'shipping_zone',
        'shipping_amount',
        'shipping_address',
        'paid_at',
        'payload',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'payload' => 'array',
        'shipping_address' => 'array',
    ];

    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }
}
