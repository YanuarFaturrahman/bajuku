<?php

namespace App\Livewire\Shop;

use App\Livewire\Concerns\HandlesWishlist;
use App\Livewire\Concerns\Toasts;
use App\Models\CartItem;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ProductDetail extends Component
{
    use Toasts, HandlesWishlist;

    public Product $product;
    public string $color = '';
    public string $size = '';
    public int $qty = 1;
    public string $tab = 'deskripsi';

    public function mount(string $slug): void
    {
        $this->product = Product::active()->where('slug', $slug)->firstOrFail();

        // Pilih varian pertama yang masih ada stok sebagai default
        $variant = $this->product->variants()->where('stock', '>', 0)->first()
            ?? $this->product->variants()->first();

        $this->color = $variant->color ?? '';
        $this->size  = $variant->size ?? '';
    }

    /** Varian terpilih (kombinasi warna + ukuran). */
    #[Computed]
    public function variant()
    {
        return $this->product->variants()->where('color', $this->color)->where('size', $this->size)->first();
    }

    /** Daftar warna yang tersedia untuk produk ini. */
    #[Computed]
    public function availableColors()
    {
        return $this->product->variants()->pluck('color')->unique()->values();
    }

    /** Ukuran yang tersedia untuk warna terpilih: [ukuran => stok]. */
    #[Computed]
    public function sizesForColor()
    {
        return $this->product->variants()->where('color', $this->color)->pluck('stock', 'size');
    }

    public function selectColor(string $color): void
    {
        $this->color = $color;
        $this->size  = (string) ($this->sizesForColor->filter(fn ($s) => $s > 0)->keys()->first()
            ?? $this->sizesForColor->keys()->first());
        $this->qty = 1;
    }

    public function selectSize(string $size): void
    {
        $this->size = $size;
        $this->qty = 1;
    }

    public function increment(): void
    {
        $max = $this->variant?->stock ?? 1;
        $this->qty = min($this->qty + 1, max($max, 1));
    }

    public function decrement(): void
    {
        $this->qty = max(1, $this->qty - 1);
    }

    public function addToCart(bool $buyNow = false)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }
        if (auth()->user()->role !== 'konsumen') {
            $this->toast('Hanya akun Konsumen yang dapat berbelanja.', 'error');
            return;
        }

        $variant = $this->variant;
        if (! $variant || $variant->stock < 1) {
            $this->toast('Varian ini sedang habis.', 'error');
            return;
        }

        $item = CartItem::firstOrNew(['user_id' => auth()->id(), 'product_variant_id' => $variant->id]);
        $newQty = ($item->exists ? $item->quantity : 0) + $this->qty;

        if ($newQty > $variant->stock) {
            $this->toast('Jumlah melebihi stok tersedia (' . $variant->stock . ').', 'error');
            return;
        }

        $item->quantity = $newQty;
        $item->save();

        $this->dispatch('cart-updated');

        if ($buyNow) {
            session(['checkout_ids' => [$item->id]]);
            return redirect()->route('checkout');
        }

        $this->toast('Ditambahkan ke keranjang');
    }

    public function render()
    {
        $reviews = $this->product->reviews()->with('user')->latest()->get();

        return view('livewire.shop.product-detail', [
            'reviews'  => $reviews,
            'average'  => round((float) $reviews->avg('rating'), 1),
            'wished'   => in_array($this->product->id, $this->wishlistIds()),
        ])->layout('components.layouts.app')->title($this->product->name);
    }
}
