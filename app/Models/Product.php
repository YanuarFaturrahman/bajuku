<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
 
class Product extends Model
{
    protected $guarded = [];
 
    protected $casts = ['is_active' => 'boolean'];
 
    public function category(): BelongsTo  { return $this->belongsTo(Category::class); }
    public function variants(): HasMany    { return $this->hasMany(ProductVariant::class); }
    public function reviews(): HasMany     { return $this->hasMany(Review::class); }
 
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
 
    /** Harga yang benar-benar dibayar (diskon jika ada). */
    public function getFinalPriceAttribute(): int
    {
        return (int) ($this->discount_price ?: $this->price);
    }
 
    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_price && $this->discount_price < $this->price;
    }
 
    public function getDiscountPercentAttribute(): int
    {
        return $this->has_discount ? (int) round((1 - $this->discount_price / $this->price) * 100) 
: 0;
    }
 
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
 
    public function getTotalStockAttribute(): int
    {
          return (int) $this->variants->sum('stock');
    }
 
    public function getAverageRatingAttribute(): float
    {
        return round((float) $this->reviews->avg('rating'), 1);
    }
}