<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Artwork extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'image_url',
        'technique',
        'dimensions',
        'year',
        'is_published',
        'vendido_at',
    ];

    protected $casts = [
        'vendido_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function isSold(): bool
    {
        return !is_null($this->vendido_at);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
