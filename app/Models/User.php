<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
 
class User extends Authenticatable
{
    use HasFactory, Notifiable;
 
    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'is_active', 'last_login_at',
    ];
 
    protected $hidden = ['password', 'remember_token'];
 
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'is_active'         => 'boolean',
            'password'          => 'hashed',   // otomatis di-hash saat disimpan
        ];
    }
 
    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }
 
    /** Nama route dashboard sesuai role (dipakai setelah login). */
     public function dashboardRoute(): string
    {
        return match ($this->role) {
            'admin'      => 'admin.dashboard',
            'gudang'     => 'warehouse.dashboard',
            'supplier'   => 'supplier.dashboard',
            'owner'      => 'owner.dashboard',
            'superadmin' => 'superadmin.dashboard',
            default      => 'home',
        };
    }
 
    public function getRoleLabelAttribute(): string
    {
        return config('rbac.roles')[$this->role] ?? ucfirst($this->role);
    }
 
    public function addresses(): HasMany  { return $this->hasMany(Address::class); }
    public function cartItems(): HasMany  { return $this->hasMany(CartItem::class); }
    public function wishlists(): HasMany  { return $this->hasMany(Wishlist::class); }
    public function orders(): HasMany     { return $this->hasMany(Order::class); }
    public function reviews(): HasMany    { return $this->hasMany(Review::class); }
    public function supplier(): HasOne    { return $this->hasOne(Supplier::class); }
}