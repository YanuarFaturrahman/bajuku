<?php

namespace App\Livewire\Concerns;

use App\Models\Wishlist;

trait HandlesWishlist
{
    /** Tombol hati pada kartu produk. */
    public function toggleWishlist(int $productId)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $existing = Wishlist::where('user_id', auth()->id())->where('product_id', $productId)->first();

        if ($existing) {
            $existing->delete();
            $this->toast('Dihapus dari wishlist');
        } else {
            Wishlist::create(['user_id' => auth()->id(), 'product_id' => $productId]);
            $this->toast('Ditambahkan ke wishlist');
        }
    }

    /** Daftar id produk yang sudah di-wishlist user (untuk mewarnai ikon hati). */
    public function wishlistIds(): array
    {
        return auth()->check()
            ? Wishlist::where('user_id', auth()->id())->pluck('product_id')->all()
            : [];
    }
}
