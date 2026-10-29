<?php

namespace App\Livewire\Account;

use App\Livewire\Concerns\Toasts;
use App\Models\Wishlist as WishlistItem;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Wishlist')]
class Wishlist extends Component
{
    use Toasts;

    public function remove(int $id): void
    {
        WishlistItem::where('user_id', auth()->id())->where('id', $id)->delete();
        $this->toast('Dihapus dari wishlist');
    }

    public function render()
    {
        return view('livewire.account.wishlist', [
            'items' => WishlistItem::with('product.category')->where('user_id', auth()->id())->latest()->get(),
        ])->layout('components.layouts.app');
    }
}
