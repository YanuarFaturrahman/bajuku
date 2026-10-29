<?php
 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 
class Address extends Model
{
    protected $guarded = [];
 
    protected $casts = ['is_default' => 'boolean'];
 
    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->city} {$this->postal_code}";
    }
}