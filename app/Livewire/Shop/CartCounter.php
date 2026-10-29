<?php

namespace App\Livewire\Shop;

use App\Models\CartItem;
use Livewire\Attributes\On;
use Livewire\Component;

class CartCounter extends Component
{
    // Event 'cart-updated' dikirim dari komponen lain; cukup memicu render ulang badge
    #[On('cart-updated')]
    public function refreshCount(): void {}

    public function render()
    {
        $count = auth()->check() && auth()->user()->role === 'konsumen'
            ? (int) CartItem::where('user_id', auth()->id())->sum('quantity')
            : 0;

        return view('livewire.shop.cart-counter', compact('count'));
    }
}
