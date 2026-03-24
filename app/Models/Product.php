<?php

namespace App\Models;

use App\Models\Vendor;
use App\Models\Customer\CartItem;
use App\Models\Customer\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'name',
        'description',
        'price',
        'stock',
        'is_active',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'stock' => 'integer',
    ];

    /**
     * Get the vendor that owns the product.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include products in stock.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Check if a specific quantity is in stock.
     * If quantity is null, checks if at least one unit is available.
     */
    public function isInStock(?int $quantity = null): bool
    {
        return $quantity === null ? $this->stock > 0 : $this->stock >= $quantity;
    }

    /**
     * Check if the product is out of stock.
     * If quantity is provided, checks if stock is insufficient for that quantity.
     * If quantity is null, checks for absolute zero stock.
     */
    public function isOutOfStock(?int $quantity = null): bool
    {
        return $quantity === null ? $this->stock <= 0 : $this->stock < $quantity;
    }
}
