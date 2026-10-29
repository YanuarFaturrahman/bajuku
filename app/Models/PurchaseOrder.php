<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
 
class PurchaseOrder extends Model
{
    protected $guarded = [];
 
    protected $casts = [
        'expected_date'        => 'date',
        'due_date'             => 'date',
        'estimated_ready_date' => 'date',
        'shipped_at'           => 'datetime',
        'received_at'          => 'datetime',
        'paid_at'              => 'datetime',
    ];
 
    public const STATUSES = [
        'sent'      => ['Menunggu Konfirmasi', 'badge-amber'],
        'confirmed' => ['Diproses Supplier', 'badge-blue'],
        'shipped'   => ['Dalam Pengiriman', 'badge-sky'],
        'received'  => ['Selesai / Diterima', 'badge-green'],
        'rejected'  => ['Ditolak Supplier', 'badge-red'],
        'cancelled' => ['Dibatalkan', 'badge-gray'],
    ];
 
    public function supplier(): BelongsTo  { return $this->belongsTo(Supplier::class); }
    public function creator(): BelongsTo   { return $this->belongsTo(User::class, 'created_by'); }
    public function items(): HasMany       { return $this->hasMany(PurchaseOrderItem::class); }
 
    public function getStatusLabelAttribute(): string { return self::STATUSES[$this->status][0] ?? 
$this->status; }
    public function getStatusBadgeAttribute(): string { return self::STATUSES[$this->status][1] ?? 
'badge-gray'; }
 
    public static function generateNumber(): string
    {
        return 'PO-' . now()->format('Ymd') . '-' . str_pad((string) 
(static::whereDate('created_at', today())->count() + 1), 3, '0', STR_PAD_LEFT);
}
}