<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price', 'stock', 'sizes', 'images', 'is_active',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'sizes'     => 'array',
        'images'    => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFirstImageAttribute(): ?string
    {
        return $this->images[0] ?? null;
    }
}
