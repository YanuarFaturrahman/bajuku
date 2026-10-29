<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class CartItem extends Model
{
    protected $guarded = [];
 
    public function variant(): BelongsTo { return $this->belongsTo(ProductVariant::class, 
'product_variant_id'); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
 
    public function getLineTotalAttribute(): int
    {
        return $this->variant->product->final_price * $this->quantity;
    }
}