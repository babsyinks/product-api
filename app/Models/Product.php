<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price'      => 'decimal:2',
        'stock'      => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Scopes

    /**
     * Filter by name or description (full-text search).
     */
    public function scopeSearch($query, ?string $term): void
    {
        if (blank($term)) {
            return;
        }

        $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Filter products within a price range.
     */
    public function scopePriceRange($query, ?float $min, ?float $max): void
    {
        if ($min !== null) {
            $query->where('price', '>=', $min);
        }

        if ($max !== null) {
            $query->where('price', '<=', $max);
        }
    }

    /**
     * Filter products that are in stock.
     */
    public function scopeInStock($query, ?bool $inStock): void
    {
        if ($inStock === true) {
            $query->where('stock', '>', 0);
        } elseif ($inStock === false) {
            $query->where('stock', '=', 0);
        }
    }
}
