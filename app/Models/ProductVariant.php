<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
 
class ProductVariant extends Model
{
    protected $guarded = [];
 
    public function product(): BelongsTo   { return $this->belongsTo(Product::class); }
    public function orderItems(): HasMany  { return $this->hasMany(OrderItem::class); }
 
    /** Contoh: "Pink / M" */
    public function getLabelAttribute(): string
    {
        return $this->color . ' / ' . $this->size;
    }
 
    public function getIsLowStockAttribute(): bool
    {
        return $this->stock <= config('shop.low_stock');
    }
}