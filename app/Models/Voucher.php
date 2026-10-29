<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Voucher extends Model
{
    protected $guarded = [];
 
    protected $casts = [
        'is_active'  => 'boolean',
        'expires_at' => 'date',
    ];
 
    /**
     * Cek apakah voucher bisa dipakai untuk subtotal tertentu.
     * Mengembalikan [bool, pesan].
     */
    public function check(int $subtotal): array
    {
        if (! $this->is_active)                                   return [false, 'Voucher tidak 
aktif.'];
        if ($this->expires_at && $this->expires_at->isPast()
            && ! $this->expires_at->isToday())                    return [false, 'Voucher sudah 
kedaluwarsa.'];
        if ($this->used >= $this->quota)                          return [false, 'Kuota voucher 
sudah habis.'];
        if ($subtotal < $this->min_purchase)                      return [false, 'Minimal belanja 
' . rupiah($this->min_purchase) . '.'];
 
        return [true, 'Voucher berhasil dipakai.'];
    }
 
    /** Hitung nominal potongan. */
    public function discountFor(int $subtotal): int
    {
        $discount = $this->type === 'percent'
            ? (int) floor($subtotal * $this->value / 100)
            : (int) $this->value;
 
        if ($this->max_discount) {
            $discount = min($discount, (int) $this->max_discount);
               }
 
        return min($discount, $subtotal);
    }
 
    public function getLabelAttribute(): string
    {
        return $this->type === 'percent' ? "{$this->value}%" : rupiah($this->value);
    }
}