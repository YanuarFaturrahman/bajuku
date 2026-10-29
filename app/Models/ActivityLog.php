<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class ActivityLog extends Model
{
    protected $guarded = [];
 
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
 
    /** Rekam aktivitas pengguna ke audit trail. */
    public static function record(string $action, ?string $description = null): void
    {
        static::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'description' => $description,
            'ip_address'  => request()->ip(),
        ]);
    }
}