<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <h1 class="mb-8 font-display text-3xl">Keranjang Belanja</h1>

    @if ($this->items->isEmpty())
        <div class="rounded-3xl border border-dashed border-brand-200 py-24 text-center">
            <p class="font-display text-2xl">Keranjangmu masih kosong</p>
            <p class="mb-6 mt-2 text-sm text-gray-400">Yuk, temukan baju favoritmu.</p>
            <a href="{{ route('catalog') }}" class="btn-primary">Mulai Belanja</a>
        </div>
    @else
        <div class="grid gap-8 lg:grid-cols-[1fr_340px]">
            <div class="space-y-4">
                @foreach ($this->items as $item)
                    @php($p = $item->variant->product)
                    <div wire:key="cart-{{ $item->id }}" class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-4">
                        <input type="checkbox" value="{{ $item->id }}" wire:model.live="selected" class="h-4 w-4 rounded accent-pink-500">
                        <div class="h-24 w-20 shrink-0 overflow-hidden rounded-xl"><x-product-image :product="$p" /></div>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('product.show', $p->slug) }}" class="block truncate font-medium text-gray-800 hover:text-brand-600">{{ $p->name }}</a>
                            <p class="text-xs text-gray-400">{{ $item->variant->label }} · stok {{ $item->variant->stock }}</p>
                            <p class="mt-1 text-sm font-semibold text-brand-600">{{ rupiah($p->final_price) }}</p>
                        </div>
                        <div class="flex items-center rounded-full border border-gray-200">
                            <button wire:click="decrement({{ $item->id }})" class="h-8 w-8 hover:text-brand-600">−</button>
                            <span class="w-8 text-center text-sm">{{ $item->quantity }}</span>
                            <button wire:click="increment({{ $item->id }})" class="h-8 w-8 hover:text-brand-600">+</button>
                        </div>
                        <p class="hidden w-28 text-right text-sm font-semibold sm:block">{{ rupiah($item->line_total) }}</p>
                        <button wire:click="remove({{ $item->id }})" wire:confirm="Hapus produk ini dari keranjang?" class="text-gray-300 hover:text-red-500">✕</button>
                    </div>
                @endforeach
            </div>

            <aside class="h-fit rounded-2xl border border-brand-100 bg-brand-50/50 p-6 lg:sticky lg:top-28">
                <h2 class="font-display text-xl">Ringkasan</h2>
                <div class="mt-5 flex justify-between text-sm"><span class="text-gray-500">Produk dipilih</span><span>{{ count($selected) }} item</span></div>
                <div class="mt-3 flex justify-between border-t border-brand-100 pt-4"><span class="text-gray-500">Subtotal</span><span class="font-display text-xl text-brand-600">{{ rupiah($this->subtotal) }}</span></div>
                <button wire:click="checkout" class="btn-primary mt-6 w-full">Lanjut ke Checkout</button>
            </aside>
        </div>
    @endif
</div>
