<?php

namespace App\Livewire\Shop;

use App\Livewire\Concerns\HandlesWishlist;
use App\Livewire\Concerns\Toasts;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Beranda')]
class Home extends Component
{
    use Toasts, HandlesWishlist;

    public function render()
    {
        $with = ['category', 'reviews'];

        $categories = Category::whereNull('parent_id')
            ->withCount('products')
            ->get();

        $flashSale = Product::active()
            ->with($with)
            ->whereNotNull('discount_price')
            ->latest()
            ->take(8)
            ->get();

        $trending = Product::active()
            ->with($with)
            ->orderByDesc('sold_count')
            ->take(8)
            ->get();

        $newest = Product::active()
            ->with($with)
            ->latest()
            ->take(8)
            ->get();

        return view('livewire.shop.home', [
            'categories' => $categories,
            'flashSale'  => $flashSale,
            'trending'   => $trending,
            'newest'     => $newest,
            'wishlist'   => $this->wishlistIds(),
        ])->layout('components.layouts.app');
    }
}
