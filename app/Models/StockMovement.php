<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class StockMovement extends Model
{
    protected $guarded = [];
 
    public function variant(): BelongsTo { return $this->belongsTo(ProductVariant::class, 
'product_variant_id'); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
 
    /**
     * Catat pergerakan stok. Panggil SETELAH kolom stock pada varian diubah.
     * $quantity bertanda: positif = masuk, negatif = keluar.
     */
     public static function record(ProductVariant $variant, string $type, int $quantity, ?string 
$reference = null, ?string $note = null): self
    {
        $variant->refresh();
 
        return static::create([
            'product_variant_id' => $variant->id,
            'user_id'            => auth()->id(),
            'type'               => $type,
            'quantity'           => $quantity,
            'stock_after'        => $variant->stock,
            'reference'          => $reference,
            'note'               => $note,
        ]);
    }
}