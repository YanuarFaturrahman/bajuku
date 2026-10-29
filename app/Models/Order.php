<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
 
class Order extends Model
{
    protected $guarded = [];
 
    protected $casts = [
        'paid_at'      => 'datetime',
        'packed_at'    => 'datetime',
        'shipped_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];
 
    /** Status => [label, kelas badge] */
    public const STATUSES = [
        'pending_payment'  => ['Menunggu Pembayaran', 'badge-amber'],
        'to_process'       => ['Perlu Diproses', 'badge-blue'],
        'packing'          => ['Sedang Dikemas', 'badge-violet'],
        'shipped'          => ['Dikirim', 'badge-sky'],
        'completed'        => ['Selesai', 'badge-green'],
        'cancelled'        => ['Dibatalkan', 'badge-gray'],
        'return_requested' => ['Pengajuan Retur', 'badge-orange'],
        'refunded'         => ['Dikembalikan', 'badge-gray'],
    ];
 
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function items(): HasMany  { return $this->hasMany(OrderItem::class); }
       public function getStatusLabelAttribute(): string { return self::STATUSES[$this->status][0] ?? 
$this->status; }
    public function getStatusBadgeAttribute(): string { return self::STATUSES[$this->status][1] ?? 
'badge-gray'; }
 
    public static function generateNumber(): string
    {
        return 'BJK-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));
    }
 
    /** Pembayaran berhasil -> pesanan masuk antrean gudang. */
    public function markAsPaid(): void
    {
        $this->update([
            'payment_status' => 'paid',
            'status'         => 'to_process',
            'paid_at'        => now(),
        ]);
    }
 
    /** Batalkan pesanan & kembalikan stok. */
    public function cancelOrder(?string $reason = null): void
    {
        DB::transaction(function () use ($reason) {
            $this->restock('Pembatalan pesanan');
            $this->update(['status' => 'cancelled', 'cancel_reason' => $reason]);
        });
    }
 
    /** Refund retur: stok kembali, pembayaran ditandai refunded. */
    public function refund(): void
    {
        DB::transaction(function () {
            $this->restock('Retur / refund pesanan');
            $this->update(['status' => 'refunded', 'payment_status' => 'refunded']);
        });
    }
 
    protected function restock(string $note): void
    {
        foreach ($this->items()->with('variant')->get() as $item) {
            if ($item->variant) {
                $item->variant->increment('stock', $item->quantity);
                StockMovement::record($item->variant, 'in', $item->quantity, $this->order_number, 
$note);
            }
        }
    }
}