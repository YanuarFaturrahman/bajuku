<?php

namespace App\Livewire\Shop;

use App\Livewire\Concerns\Toasts;
use App\Models\CartItem;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Keranjang')]
class Cart extends Component
{
    use Toasts;

    /** id cart_items yang dicentang untuk di-checkout */
    public array $selected = [];

    public function mount(): void
    {
        $this->selected = $this->items->pluck('id')->map(fn ($id) => (string) $id)->all();
    }

    #[Computed]
    public function items()
    {
        return CartItem::with('variant.product')
            ->where('user_id', auth()->id())
            ->whereHas('variant')
            ->latest()
            ->get();
    }

    #[Computed]
    public function subtotal(): int
    {
        return (int) $this->items
            ->whereIn('id', array_map('intval', $this->selected))
            ->sum(fn ($i) => $i->line_total);
    }

    public function increment(int $id): void
    {
        $item = CartItem::where('user_id', auth()->id())->with('variant')->findOrFail($id);

        if ($item->quantity + 1 > $item->variant->stock) {
            $this->toast('Stok tidak mencukupi.', 'error');
            return;
        }
        $item->increment('quantity');
        $this->dispatch('cart-updated');
    }

    public function decrement(int $id): void
    {
        $item = CartItem::where('user_id', auth()->id())->findOrFail($id);

        if ($item->quantity > 1) {
            $item->decrement('quantity');
            $this->dispatch('cart-updated');
        }
    }

    public function remove(int $id): void
    {
        CartItem::where('user_id', auth()->id())->where('id', $id)->delete();
        $this->selected = array_values(array_diff($this->selected, [(string) $id]));
        $this->dispatch('cart-updated');
        $this->toast('Produk dihapus dari keranjang');
    }

    public function checkout()
    {
        $ids = array_map('intval', $this->selected);

        if (empty($ids)) {
            $this->toast('Pilih minimal satu produk.', 'error');
            return;
        }

        foreach ($this->items->whereIn('id', $ids) as $item) {
            if ($item->quantity > $item->variant->stock) {
                $this->toast($item->variant->product->name . ' melebihi stok tersedia.', 'error');
                return;
            }
        }

        session(['checkout_ids' => $ids]);
        return redirect()->route('checkout');
    }

    public function render()
    {
        return view('livewire.shop.cart')->layout('components.layouts.app');
    }
}
